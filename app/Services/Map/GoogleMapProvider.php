<?php

namespace App\Services\Map;

use App\Models\BusinessSetting;
use Illuminate\Support\Facades\Http;

/**
 * GoogleMapProvider
 *
 * Uses current Google Maps / Places / Routes API calls.
 * Returns raw JSON arrays compatible with existing endpoints.
 */
class GoogleMapProvider implements MapProviderInterface
{
    protected ?string $apiKeyServer;

    public function __construct()
    {
        $this->apiKeyServer = BusinessSetting::where('key', 'map_api_key_server')->first()?->value
            ?? config('maps.google.api_key_server');
    }

    /**
    * Place autocomplete using Google Places Autocomplete endpoint.
    *
    * This method mirrors the previous ConfigController implementation to keep
    * response shape identical.
    */
    public function autocomplete(string $input, ?string $language = null): array
    {
        $apiKey = $this->apiKeyServer;
        $url = 'https://places.googleapis.com/v1/places:autocomplete';

        $data = [
            'input' => $input,
            'languageCode' => $language,
        ];

        $headers = [
            'Content-Type: application/json',
            'X-Goog-Api-Key: ' . $apiKey,
        ];

        // OLD GOOGLE MAPS IMPLEMENTATION (commented out, kept for reference)
        // $ch = curl_init($url);
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // curl_setopt($ch, CURLOPT_POST, true);
        // curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        // curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        // $response = curl_exec($ch);
        // curl_close($ch);
        // return json_decode($response, true);

        // New implementation leveraging Http facade while maintaining response shape
        $response = Http::withHeaders($headers)
            ->post($url, $data);

        return $response->json();
    }

    /**
     * Place details using Google Places API.
     */
    public function placeDetails(string $placeId, ?string $language = null): array
    {
        $apiKey = $this->apiKeyServer;
        $url = 'https://places.googleapis.com/v1/places/' . $placeId;

        $headers = [
            'Content-Type: application/json',
            'X-Goog-Api-Key: ' . $apiKey,
            'X-Goog-FieldMask: id,displayName,formattedAddress,location',
        ];

        // OLD GOOGLE MAPS IMPLEMENTATION (commented out, kept for reference)
        // $ch = curl_init();
        // curl_setopt($ch, CURLOPT_URL, $url);
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // curl_setopt($ch, CURLOPT_HTTPHEADER, [
        //     'Content-Type: application/json',
        //     'X-Goog-Api-Key: ' . $apiKey,
        //     'X-Goog-FieldMask: id,displayName,formattedAddress,location',
        // ]);
        // $response = curl_exec($ch);
        // curl_close($ch);
        // return json_decode($response, true);

        $response = Http::withHeaders($headers)->get($url);

        return $response->json();
    }

    /**
     * Distance matrix leveraging Google Routes API to mirror distance_api output.
     */
    public function routeMatrix(
        float $originLat,
        float $originLng,
        float $destLat,
        float $destLng
    ): array {
        $apiKey = $this->apiKeyServer;
        $url = 'https://routes.googleapis.com/distanceMatrix/v2:computeRouteMatrix';

        $data = [
            'origins' => [
                ['waypoint' => ['location' => ['latLng' => ['latitude' => $originLat, 'longitude' => $originLng]]]],
            ],
            'destinations' => [
                ['waypoint' => ['location' => ['latLng' => ['latitude' => $destLat, 'longitude' => $destLng]]]],
            ],
            'travelMode' => 'WALK',
        ];

        $headers = [
            'Content-Type: application/json',
            'X-Goog-Api-Key: ' . $apiKey,
            'X-Goog-FieldMask: duration,distanceMeters,localizedValues',
        ];

        // OLD GOOGLE MAPS IMPLEMENTATION (commented out, kept for reference)
        // $ch = curl_init();
        // curl_setopt($ch, CURLOPT_URL, $url);
        // curl_setopt($ch, CURLOPT_POST, true);
        // curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        // $response = curl_exec($ch);
        // curl_close($ch);
        // return json_decode($response, true)[0];

        $response = Http::withHeaders($headers)->post($url, $data);
        $payload = $response->json();

        return $payload[0] ?? [];
    }

    /**
     * Reverse geocoding via Google Geocode API.
     */
    public function geocode(float $lat, float $lng, ?string $language = null): array
    {
        $response = Http::get(
            'https://maps.googleapis.com/maps/api/geocode/json',
            [
                'latlng' => $lat . ',' . $lng,
                'key' => $this->apiKeyServer,
                'language' => $language,
            ]
        );

        return $response->json();
    }

    /**
     * Web configuration for blade views when using Google Maps.
     */
    public function webMapConfig(): array
    {
        return [
            'driver' => 'google',
            'js_url' => sprintf(
                'https://maps.googleapis.com/maps/api/js?key=%s&libraries=drawing,places&v=3.45.8',
                BusinessSetting::where('key', 'map_api_key')->first()?->value
            ),
            'default_center' => config('maps.default_center'),
            'tile_url' => config('maps.tile_url'),
            'tile_urls' => config('maps.tile_urls'),
        ];
    }
}

