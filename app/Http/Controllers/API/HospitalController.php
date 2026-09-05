<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Hospital;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HospitalController extends Controller
{
    /**
     * GET /api/v1/hospitals
     * List all active hospitals, optionally filtered by type or search term.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Hospital::where('is_active', true);

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('search')) {
            $term = $request->search;
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                  ->orWhere('name_np', 'like', "%{$term}%")
                  ->orWhere('address', 'like', "%{$term}%")
                  ->orWhere('address_np', 'like', "%{$term}%");
            });
        }

        $hospitals = $query->orderBy('name')->get()->map(fn ($h) => $this->format($h));

        return response()->json([
            'success' => true,
            'data'    => $hospitals,
        ]);
    }

    /**
     * GET /api/v1/hospitals/nearby
     * Return hospitals sorted by distance from the user's coordinates.
     * Requires: lat, lng (query params).
     * Optional: radius (km, default 50), limit (default 20).
     */
    public function nearby(Request $request): JsonResponse
    {
        $request->validate([
            'lat'    => 'required|numeric|between:-90,90',
            'lng'    => 'required|numeric|between:-180,180',
            'radius' => 'sometimes|numeric|min:1|max:500',
            'limit'  => 'sometimes|integer|min:1|max:100',
        ]);

        $lat    = (float) $request->lat;
        $lng    = (float) $request->lng;
        $radius = (float) ($request->radius ?? 50);
        $limit  = (int)   ($request->limit  ?? 20);

        // Haversine formula via raw SQL — works on MySQL without extensions
        $hospitals = Hospital::where('is_active', true)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->selectRaw("
                *,
                (
                    6371 * ACOS(
                        COS(RADIANS(?)) * COS(RADIANS(latitude)) *
                        COS(RADIANS(longitude) - RADIANS(?)) +
                        SIN(RADIANS(?)) * SIN(RADIANS(latitude))
                    )
                ) AS distance_km
            ", [$lat, $lng, $lat])
            ->having('distance_km', '<=', $radius)
            ->orderBy('distance_km')
            ->limit($limit)
            ->get()
            ->map(fn ($h) => array_merge($this->format($h), [
                'distance_km' => round($h->distance_km, 2),
            ]));

        return response()->json([
            'success' => true,
            'data'    => $hospitals,
            'meta'    => [
                'lat'       => $lat,
                'lng'       => $lng,
                'radius_km' => $radius,
                'count'     => $hospitals->count(),
            ],
        ]);
    }

    /**
     * GET /api/v1/hospitals/{id}
     * Single hospital detail.
     */
    public function show(int $id): JsonResponse
    {
        $hospital = Hospital::where('is_active', true)->findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => $this->format($hospital),
        ]);
    }

    // ── Private helpers ────────────────────────────────────────────────────

    private function format(Hospital $h): array
    {
        return [
            'id'         => $h->id,
            'name'       => $h->name,
            'name_np'    => $h->name_np,
            'address'    => $h->address,
            'address_np' => $h->address_np,
            'phone'      => $h->phone,
            'type'       => $h->type,
            'latitude'   => $h->latitude,
            'longitude'  => $h->longitude,
        ];
    }
}
