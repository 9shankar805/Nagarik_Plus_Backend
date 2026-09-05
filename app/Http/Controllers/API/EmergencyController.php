<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\EmergencyContact;
use App\Models\Hospital;
use App\Services\OverpassService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class EmergencyController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $contacts = EmergencyContact::active()
                                    ->get()
                                    ->map(fn($c) => [
                                        'id'          => $c->id,
                                        'name'        => $c->name,
                                        'name_np'     => $c->name_np,
                                        'number'      => $c->number,
                                        'description' => $c->description,
                                        'category'    => $c->category,
                                        'icon'        => $c->icon,
                                        'color'       => $c->color,
                                    ]);

        $lat = $request->query('lat');
        $lng = $request->query('lng');

        $query = Hospital::where('is_active', true);

        if ($lat && $lng) {
            $query->selectRaw(
                '*, ( 6371 * acos( cos( radians(?) ) * cos( radians(latitude) ) * cos( radians(longitude) - radians(?) ) + sin( radians(?) ) * sin( radians(latitude) ) ) ) AS distance_km',
                [$lat, $lng, $lat]
            )->orderBy('distance_km');
        }

        $dbHospitals = $query->get();

        if ($dbHospitals->isNotEmpty()) {
            $hospitals = $dbHospitals->map(fn($h) => [
                'id' => $h->id,
                'name' => $h->name,
                'name_np' => $h->name_np,
                'address' => $h->address,
                'address_np' => $h->address_np,
                'phone' => $h->phone,
                'type' => $h->type,
                'lat' => (float) $h->latitude,
                'lng' => (float) $h->longitude,
                'distance_km' => isset($h->distance_km) ? round((float) $h->distance_km, 2) : null,
            ]);
        } else {
            $hospitals = [
                [
                    'id' => 1,
                    'name' => 'Teaching Hospital',
                    'name_np' => 'शिक्षण अस्पताल',
                    'address' => 'Maharajgunj, Kathmandu',
                    'address_np' => 'महाराजगञ्ज, काठमाडौं',
                    'phone' => '01-4412765',
                    'type' => 'Government',
                    'lat' => 27.7345,
                    'lng' => 85.3283,
                ],
                [
                    'id' => 2,
                    'name' => 'Bir Hospital',
                    'name_np' => 'वीर अस्पताल',
                    'address' => 'Tundikhel, Kathmandu',
                    'address_np' => 'टुण्डिखेल, काठमाडौं',
                    'phone' => '01-4261944',
                    'type' => 'Government',
                    'lat' => 27.7058,
                    'lng' => 85.3146,
                ],
                [
                    'id' => 3,
                    'name' => 'Narayani Zonal Hospital',
                    'name_np' => 'नारायणी अञ्चल अस्पताल',
                    'address' => 'Birgunj, Parsa',
                    'address_np' => 'वीरगञ्ज, पर्सा',
                    'phone' => '051-520111',
                    'type' => 'Government',
                    'lat' => 27.0122,
                    'lng' => 84.8778,
                ],
                [
                    'id' => 4,
                    'name' => 'Norvic Hospital',
                    'name_np' => 'नोरभिक अस्पताल',
                    'address' => 'Thapathali, Kathmandu',
                    'address_np' => 'थापाथली, काठमाडौं',
                    'phone' => '01-4584242',
                    'type' => 'Private',
                    'lat' => 27.6946,
                    'lng' => 85.3197,
                ],
            ];

            if ($lat && $lng) {
                foreach ($hospitals as &$h) {
                    $dLat = deg2rad($h['lat'] - $lat);
                    $dLon = deg2rad($h['lng'] - $lng);
                    $a = sin($dLat / 2) * sin($dLat / 2) + cos(deg2rad($lat)) * cos(deg2rad($h['lat'])) * sin($dLon / 2) * sin($dLon / 2);
                    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
                    $h['distance_km'] = round(6371 * $c, 2);
                }
                usort($hospitals, fn($a, $b) => $a['distance_km'] <=> $b['distance_km']);
            }
        }

        return response()->json([
            'success' => true,
            'data' => [
                'contacts' => $contacts,
                'hospitals' => $hospitals,
            ],
        ]);
    }

    /**
     * Fetch nearby emergency facilities using OpenStreetMap.
     */
    public function nearby(Request $request, OverpassService $overpassService): JsonResponse
    {
        $request->validate([
            'lat'    => 'required|numeric',
            'lng'    => 'required|numeric',
            'radius' => 'nullable|integer|min:100|max:10000',
        ]);

        $lat = (float) $request->lat;
        $lng = (float) $request->lng;
        $radius = (int) $request->input('radius', 5000);

        // Round coordinates to ~2 decimal places (approx 1.1km) to aggregate cache hits
        $cacheLat = round($lat, 2);
        $cacheLng = round($lng, 2);
        
        $cacheKey = "osm_emergency_{$cacheLat}_{$cacheLng}_{$radius}";

        // Cache for 24 hours
        $facilities = Cache::remember($cacheKey, 86400, function () use ($overpassService, $lat, $lng, $radius) {
            return $overpassService->getNearbyFacilities($lat, $lng, $radius);
        });

        return response()->json([
            'success' => true,
            'data'    => $facilities,
        ]);
    }
}
