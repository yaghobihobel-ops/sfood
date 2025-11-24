<?php

namespace Modules\Iran\Services;

use App\Services\Map\MapProviderInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Arr;

/**
 * IranMapProvider
 *
 * Implements MapProviderInterface using Neshan (Iran) APIs while
 * shaping responses to mimic Google endpoints consumed by existing clients.
 */
class IranMapProvider implements MapProviderInterface
{
    protected ?string $apiKey;
    protected string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('maps.neshan.api_key');
        $this->baseUrl = rtrim(config('maps.neshan.base_url', 'https://api.neshan.org'), '/');
    }

    /**
     * Place autocomplete mapped from Neshan Search API.
     *
     * Neshan search returns a collection of items with title/address/location.
     * We expose a Google-like suggestion payload where placeId encodes latitude
     * and longitude ("lat,lng") so it can be reused in placeDetails.
     */
    public function autocomplete(string $input, ?string $language = null): array
    {
        $defaultCenter = config('maps.default_center');
        $response = Http::withHeaders([
            'Api-Key' => $this->apiKey,
        ])->get(
            $this->baseUrl . '/v1/search',
            [
                'term' => $input,
                'lat' => $defaultCenter['lat'] ?? 35.6892,
                'lng' => $defaultCenter['lng'] ?? 51.3890,
            ]
        );

        $items = $response->json('items', []);

        $suggestions = [];
        foreach ($items as $item) {
            $lat = Arr::get($item, 'location.y');
            $lng = Arr::get($item, 'location.x');
            $placeId = $lat && $lng ? $lat . ',' . $lng : (string) ($item['id'] ?? md5(json_encode($item)));

            $suggestions[] = [
                'placePrediction' => [
                    'placeId' => $placeId,
                    'id' => $placeId,
                    'displayName' => [
                        'text' => $item['title'] ?? ($item['name'] ?? ''),
                    ],
                    'structuredFormat' => [
                        'mainText' => $item['title'] ?? ($item['name'] ?? ''),
                        'secondaryText' => $item['address'] ?? ($item['region'] ?? ''),
                    ],
                    'formattedAddress' => $item['address'] ?? null,
                    'location' => [
                        'latitude' => $lat,
                        'longitude' => $lng,
                    ],
                ],
            ];
        }

        return [
            'suggestions' => $suggestions,
        ];
    }

    /**
     * Place details rely on the placeId encoding of latitude,longitude created in autocomplete.
     *
     * If coordinates cannot be parsed, the result still returns a Google-like skeleton
     * with null values to avoid breaking clients.
     */
    public function placeDetails(string $placeId, ?string $language = null): array
    {
        $coordinates = $this->parsePlaceIdCoordinates($placeId);

        $lat = $coordinates['lat'];
        $lng = $coordinates['lng'];

        $addressData = $lat && $lng ? $this->reverseGeocode($lat, $lng) : [];
        $formattedAddress = Arr::get($addressData, 'formatted_address');

        return [
            'id' => $placeId,
            'displayName' => [
                'text' => Arr::get($addressData, 'name') ?? $formattedAddress,
            ],
            'formattedAddress' => $formattedAddress,
            'location' => [
                'latitude' => $lat,
                'longitude' => $lng,
            ],
        ];
    }

    /**
     * Route matrix mapped from Neshan direction endpoint.
     *
     * The response is normalized to include distanceMeters and duration keys to match
     * Google's Route Matrix contract.
     */
    public function routeMatrix(
        float $originLat,
        float $originLng,
        float $destLat,
        float $destLng
    ): array {
        $response = Http::withHeaders([
            'Api-Key' => $this->apiKey,
        ])->get($this->baseUrl . '/v4/direction', [
            'type' => 'car',
            'origin' => $originLat . ',' . $originLng,
            'destination' => $destLat . ',' . $destLng,
            'avoidTrafficZone' => false,
            'avoidOddEvenZone' => false,
            'alternative' => false,
        ]);

        $leg = Arr::get($response->json(), 'routes.0.legs.0', []);

        $distanceValue = Arr::get($leg, 'distance.value');
        $durationValue = Arr::get($leg, 'duration.value');

        return [
            'distanceMeters' => $distanceValue,
            'duration' => [
                'text' => Arr::get($leg, 'duration.text'),
                'value' => $durationValue,
            ],
            'localizedValues' => [
                'distance' => [
                    'text' => Arr::get($leg, 'distance.text'),
                    'value' => $distanceValue,
                ],
                'duration' => [
                    'text' => Arr::get($leg, 'duration.text'),
                    'value' => $durationValue,
                ],
            ],
        ];
    }

    /**
     * Reverse geocode via Neshan reverse endpoint and map to Google-like results array.
     */
    public function geocode(float $lat, float $lng, ?string $language = null): array
    {
        $reverse = $this->reverseGeocode($lat, $lng);

        return [
            'results' => [
                [
                    'formatted_address' => Arr::get($reverse, 'formatted_address'),
                    'geometry' => [
                        'location' => [
                            'lat' => $lat,
                            'lng' => $lng,
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * Web configuration for blade views when using an Iranian provider.
     *
     * JS integration will be added later; for now we expose driver and defaults.
     */
    public function webMapConfig(): array
    {
        return [
            'driver' => 'iran',
            'js_url' => null,
            'default_center' => config('maps.default_center'),
        ];
    }

    /**
     * Attempt to extract latitude and longitude from the encoded placeId.
     */
    protected function parsePlaceIdCoordinates(string $placeId): array
    {
        $parts = explode(',', $placeId);

        if (count($parts) === 2 && is_numeric($parts[0]) && is_numeric($parts[1])) {
            return ['lat' => (float) $parts[0], 'lng' => (float) $parts[1]];
        }

        $decoded = base64_decode($placeId, true);
        if ($decoded) {
            $decodedParts = explode(',', $decoded);
            if (count($decodedParts) === 2 && is_numeric($decodedParts[0]) && is_numeric($decodedParts[1])) {
                return ['lat' => (float) $decodedParts[0], 'lng' => (float) $decodedParts[1]];
            }
        }

        return ['lat' => null, 'lng' => null];
    }

    /**
     * Reverse geocoding helper used by placeDetails and geocode.
     */
    protected function reverseGeocode(?float $lat, ?float $lng): array
    {
        if ($lat === null || $lng === null) {
            return [];
        }

        $response = Http::withHeaders([
            'Api-Key' => $this->apiKey,
        ])->get($this->baseUrl . '/v5/reverse', [
            'lat' => $lat,
            'lng' => $lng,
        ]);

        return $response->json();
    }
}

