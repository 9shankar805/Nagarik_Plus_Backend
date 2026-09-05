<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\CitizenService;
use App\Models\Office;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CitizenServiceController extends Controller
{
    /**
     * List all citizen services
     */
    public function index(Request $request): JsonResponse
    {
        $query = CitizenService::active();

        if ($request->category) {
            $query->where('category', $request->category);
        }
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%")
                  ->orWhere('description', 'like', "%{$request->search}%");
            });
        }

        $services = $query->get();

        return response()->json([
            'success' => true, 
            'data' => \App\Http\Resources\CitizenServiceResource::collection($services)
        ]);
    }

    /**
     * Get service detail (full guide)
     */
    public function show(string $slug): JsonResponse
    {
        $service = CitizenService::where('slug', $slug)->where('is_active', true)->firstOrFail();

        return response()->json([
            'success' => true,
            'data'    => new \App\Http\Resources\CitizenServiceResource($service),
        ]);
    }

    /**
     * Get offices for a service
     */
    public function offices(string $slug, Request $request): JsonResponse
    {
        $service = CitizenService::where('slug', $slug)->firstOrFail();

        $query = $service->offices()->where('is_active', true);

        if ($request->district) {
            $query->where('district', $request->district);
        }

        $offices = $query->get()->map(fn($o) => [
            'id'           => $o->id,
            'name'         => $o->name,
            'address'      => $o->address,
            'district'     => $o->district,
            'province'     => $o->province,
            'latitude'     => $o->latitude,
            'longitude'    => $o->longitude,
            'phone'        => $o->phone,
            'office_hours' => $o->office_hours,
        ]);

        return response()->json(['success' => true, 'data' => $offices]);
    }

    // ── All offices (general locator) ────────────────────────────────────────

    public function allOffices(Request $request): JsonResponse
    {
        if ($request->lat && $request->lng) {
            $lat    = (float) $request->lat;
            $lng    = (float) $request->lng;
            $radius = (float) ($request->radius_km ?? $request->radius ?? 20);

            $query = Office::active()
                ->selectRaw('*, (6371 * acos(cos(radians(?)) * cos(radians(latitude))
                    * cos(radians(longitude) - radians(?))
                    + sin(radians(?)) * sin(radians(latitude)))) AS distance', [$lat, $lng, $lat])
                ->having('distance', '<=', $radius)
                ->orderBy('distance');

            if ($request->category) $query->where('category', $request->category);
            if ($request->district) $query->where('district', $request->district);
        } else {
            $query = Office::active();
            if ($request->category) $query->where('category', $request->category);
            if ($request->district) $query->where('district', $request->district);
            if ($request->search) {
                $query->where(fn($q) => $q->where('name', 'like', "%{$request->search}%")
                                           ->orWhere('address', 'like', "%{$request->search}%"));
            }
        }

        $offices = $query->get()->map(fn($o) => [
            'id'           => $o->id,
            'name'         => $o->name,
            'name_np'      => $o->name_np,
            'category'     => $o->category,
            'address'      => $o->address,
            'district'     => $o->district,
            'province'     => $o->province,
            'latitude'     => $o->latitude,
            'longitude'    => $o->longitude,
            'phone'        => $o->phone,
            'email'        => $o->email,
            'website'      => $o->website,
            'office_hours' => $o->office_hours,
            'distance_km'  => $o->distance ?? null,
        ]);

        return response()->json(['success' => true, 'data' => $offices]);
    }
}
