
<?php

namespace Modules\Iran\Services;

use App\Services\Map\MapProviderInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Arr;

class IranMapProvider implements MapProviderInterface
{
    protected ?string $apiKey;
    protected string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('maps.neshan.api_key');
        $this->baseUrl = rtrim(config('maps.neshan.base_url', 'https://api.neshan.org'), '/');
    }

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

            if (!$lat || !$lng) {
                continue;
            }

            $placeId = $lat . ',' . $lng;

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

    public function webMapConfig(): array
    {
        return [
            'driver' => 'iran',
            'js_url' => null,
            'default_center' => config('maps.default_center'),
            'tile_url' => config('maps.tile_url'),
            'tile_urls' => config('maps.tile_urls'),
        ];
    }

    protected function parsePlaceIdCoordinates(string $placeId): array
    {
        $parts = explode(',', $placeId);

        if (count($parts) === 2 && is_numeric($parts[0]) && is_numeric($parts[1])) {
            return ['lat' => (float) $parts[0], 'lng' => (float) $parts[1]];
        }
        
        return ['lat' => null, 'lng' => null];
    }

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
