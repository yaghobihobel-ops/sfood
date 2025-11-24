<?php

namespace Modules\Iran\Services;

use App\Services\Map\MapProviderInterface;
use Illuminate\Support\Facades\Http;

/**
 * IranMapProvider
 *
 * Adapter for Iranian map provider (e.g., Neshan) that reshapes responses
 * to mimic the Google Maps/Places APIs used throughout the project.
 */
class IranMapProvider implements MapProviderInterface
{
    protected ?string $apiKey;
    protected string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('maps.neshan.api_key');
        $this->baseUrl = rtrim((string) config('maps.neshan.base_url', 'https://api.neshan.org'), '/');
    }

    /**
     * Place autocomplete mapped to Google-like structure.
     * The Neshan search API returns an array of items with title, address, and location.
     * We wrap those into keys that mirror Google Places Autocomplete V1.
     */
    public function autocomplete(string $input, ?string $language = null): array
    {
        $url = $this->baseUrl . '/v1/search';

        $response = Http::withHeaders([
            'Api-Key' => $this->apiKey,
        ])->get($url, [
            'term' => $input,
            // Neshan requires coordinates; fall back to Tehran default center so the API remains functional.
            'lat' => config('maps.default_center.lat'),
            'lng' => config('maps.default_center.lng'),
        ]);

        $items = $response->json() ?? [];

        $predictions = [];
        foreach ($items as $index => $item) {
            $predictions[] = [
                'id' => $item['location']['x'] ?? $index,
                'placePrediction' => [
                    'placeId' => (string) ($item['id'] ?? $index),
                    'text' => [
                        'text' => $item['title'] ?? ($item['address'] ?? ''),
                    ],
                    'structuredFormat' => [
                        'mainText' => $item['title'] ?? ($item['address'] ?? ''),
                        'secondaryText' => $item['address'] ?? null,
                    ],
                ],
                'place' => [
                    'id' => (string) ($item['id'] ?? $index),
                    'displayName' => [
                        'text' => $item['title'] ?? ($item['address'] ?? ''),
                    ],
                    'formattedAddress' => $item['address'] ?? null,
                    'location' => [
                        'latitude' => $item['location']['y'] ?? null,
                        'longitude' => $item['location']['x'] ?? null,
                    ],
                ],
            ];
        }

        return [
            'suggestions' => $predictions,
        ];
    }

    /**
     * Place details mapped to Google Place Details fields.
     * Neshan's reverse geocoding is used to fetch a readable address for lat/lng.
     */
    public function placeDetails(string $placeId, ?string $language = null): array
    {
        // Neshan search results do not expose a stable place detail endpoint by ID.
        // We expect placeId to be a JSON encoded lat,lng or a numeric identifier with location data encoded.
        // To stay compatible, parse comma-separated coordinates first; otherwise return minimal payload.
        $lat = null;
        $lng = null;
        if (str_contains($placeId, ',')) {
            [$lat, $lng] = array_map('floatval', explode(',', $placeId));
        }

        $reverse = $lat !== null && $lng !== null ? $this->geocode($lat, $lng, $language) : [];
        $formattedAddress = $reverse['results'][0]['formatted_address'] ?? ($reverse['formatted_address'] ?? null);

        return [
            'id' => $placeId,
            'displayName' => [
                'text' => $formattedAddress ?? 'Selected location',
            ],
            'formattedAddress' => $formattedAddress,
            'location' => [
                'latitude' => $lat,
                'longitude' => $lng,
            ],
        ];
    }

    /**
     * Distance matrix mapped to Google's route matrix output shape.
     * Neshan direction API returns legs with distance and duration; we map the first leg.
     */
    public function routeMatrix(
        float $originLat,
        float $originLng,
        float $destLat,
        float $destLng
    ): array {
        $url = $this->baseUrl . '/v4/direction';

        $response = Http::withHeaders([
            'Api-Key' => $this->apiKey,
        ])->get($url, [
            'origin' => $originLat . ',' . $originLng,
            'destination' => $destLat . ',' . $destLng,
            'avoidTrafficZone' => false,
            'avoidOddEvenZone' => false,
            'alternative' => false,
        ]);

        $data = $response->json() ?? [];
        $firstLeg = $data['routes'][0]['legs'][0] ?? [];

        $distanceMeters = $firstLeg['distance']['value'] ?? ($firstLeg['distance']['text'] ?? null);
        $distanceMeters = is_numeric($distanceMeters) ? (float) $distanceMeters : null;
        $durationSeconds = $firstLeg['duration']['value'] ?? null;

        return [
            'distanceMeters' => $distanceMeters,
            'duration' => $durationSeconds ? $durationSeconds . 's' : null,
            'localizedValues' => [
                'distance' => [
                    'text' => $firstLeg['distance']['text'] ?? null,
                ],
                'duration' => [
                    'text' => $firstLeg['duration']['text'] ?? null,
                ],
            ],
        ];
    }

    /**
     * Reverse geocoding mapped to Google Geocode JSON shape.
     */
    public function geocode(float $lat, float $lng, ?string $language = null): array
    {
        $url = $this->baseUrl . '/v5/reverse';

        $response = Http::withHeaders([
            'Api-Key' => $this->apiKey,
        ])->get($url, [
            'lat' => $lat,
            'lng' => $lng,
        ]);

        $data = $response->json() ?? [];
        $formatted = $data['formatted_address'] ?? ($data['address'] ?? null);

        return [
            'results' => [
                [
                    'formatted_address' => $formatted,
                    'geometry' => [
                        'location' => [
                            'lat' => $lat,
                            'lng' => $lng,
                        ],
                    ],
                    'address_components' => $data['neighbourhood'] ?? [],
                ],
            ],
            'formatted_address' => $formatted,
            'status' => $formatted ? 'OK' : 'ZERO_RESULTS',
        ];
    }

    /**
     * Provide web map configuration placeholder for Iranian provider.
     */
    public function webMapConfig(): array
    {
        return [
            'driver' => 'iran',
            'js_url' => null, // Placeholder until Neshan web SDK is integrated
            'default_center' => config('maps.default_center'),
        ];
    }
}
