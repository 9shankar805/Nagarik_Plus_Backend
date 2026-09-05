<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OverpassService
{
    /**
     * Fetch nearby medical facilities using Overpass API.
     *
     * @param float $lat
     * @param float $lng
     * @param int $radius in meters
     * @return array
     */
    public function getNearbyFacilities(float $lat, float $lng, int $radius = 5000): array
    {
        $query = <<<OVERPASS
[out:json][timeout:25];
(
  node["amenity"~"hospital|pharmacy|clinic"](around:{$radius}, {$lat}, {$lng});
  way["amenity"~"hospital|pharmacy|clinic"](around:{$radius}, {$lat}, {$lng});
  node["healthcare"~"laboratory|blood_bank"](around:{$radius}, {$lat}, {$lng});
  way["healthcare"~"laboratory|blood_bank"](around:{$radius}, {$lat}, {$lng});
);
out center tags;
OVERPASS;

        try {
            $response = Http::asForm()
                ->timeout(30)
                ->post('https://overpass-api.de/api/interpreter', [
                    'data' => $query
                ]);

            if ($response->successful()) {
                $data = $response->json();
                return $this->formatFacilities($data['elements'] ?? [], $lat, $lng);
            }

            Log::error("Overpass API error", ['status' => $response->status(), 'body' => $response->body()]);
        } catch (\Exception $e) {
            Log::error("Overpass API exception", ['error' => $e->getMessage()]);
        }

        return []; // Return empty array on failure
    }

    /**
     * Map OSM elements to a standardized array format.
     */
    private function formatFacilities(array $elements, float $centerLat, float $centerLng): array
    {
        $facilities = [];

        foreach ($elements as $el) {
            if (!isset($el['tags'])) {
                continue;
            }

            $tags = $el['tags'];
            $type = 'unknown';

            if (isset($tags['amenity'])) {
                $type = $tags['amenity'];
            } elseif (isset($tags['healthcare'])) {
                $type = $tags['healthcare'];
            }

            // Extract Name (prefer Nepali, fallback to English/default)
            $name = $tags['name:ne'] ?? $tags['name:np'] ?? $tags['name:en'] ?? $tags['name'] ?? null;
            if (!$name) {
                $name = "Unnamed " . ucfirst(str_replace('_', ' ', $type));
            }

            // Extract location from node or center of a way
            $elLat = $el['lat'] ?? $el['center']['lat'] ?? null;
            $elLng = $el['lon'] ?? $el['center']['lon'] ?? null;

            if (!$elLat || !$elLng) {
                continue;
            }

            $address = $tags['addr:full'] ?? $tags['addr:street'] ?? $tags['addr:city'] ?? '';

            // Calculate distance (Haversine formula roughly)
            $distanceKm = $this->haversineGreatCircleDistance($centerLat, $centerLng, $elLat, $elLng);

            $facilities[] = [
                'id'       => $el['id'],
                'osm_type' => $el['type'], // node, way
                'name'     => $name,
                'type'     => $type,
                'address'  => $address,
                'lat'      => (float) $elLat,
                'lng'      => (float) $elLng,
                'distance_km' => round($distanceKm, 2),
            ];
        }

        // Sort by distance
        usort($facilities, function ($a, $b) {
            return $a['distance_km'] <=> $b['distance_km'];
        });

        return $facilities;
    }

    /**
     * Calculate distance between two coordinates in kilometers.
     */
    private function haversineGreatCircleDistance($lat1, $lon1, $lat2, $lon2, $earthRadius = 6371)
    {
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c;
    }
}
