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
     * {@inheritdoc}
     */
    public function autocomplete(string $input, ?string $language = null): array
    {
        // OLD GOOGLE MAPS IMPLEMENTATION (commented out, kept for reference)
        // $apiKey = $this->apiKeyServer;
        // $url = "https://places.googleapis.com/v1/places:autocomplete";
        // $data = [
        //     "input" => $input,
        //     "languageCode" => $language ?? app()->getLocale(),
        // ];
        // $headers = [
        //     "Content-Type: application/json",
        //     "X-Goog-Api-Key: $apiKey",
        // ];
        // $ch = curl_init($url);
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // curl_setopt($ch, CURLOPT_POST, true);
        // curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        // curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        // $response = curl_exec($ch);
        // curl_close($ch);
        // return json_decode($response,true);

        $apiKey = $this->apiKeyServer;
        $url = 'https://places.googleapis.com/v1/places:autocomplete';

        $data = [
            'input' => $input,
            'languageCode' => $language ?? app()->getLocale(),
        ];

        $headers = [
            'Content-Type: application/json',
            "X-Goog-Api-Key: {$apiKey}",
        ];

        $response = Http::withHeaders($headers)->post($url, $data);

        return $response->json() ?? [];
    }

    /**
     * {@inheritdoc}
     */
    public function placeDetails(string $placeId, ?string $language = null): array
    {
        // OLD GOOGLE MAPS IMPLEMENTATION (commented out, kept for reference)
        // $apiKey = $this->apiKeyServer;
        // $url = 'https://places.googleapis.com/v1/places/'.$placeId;
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
        // return json_decode($response,true);

        $apiKey = $this->apiKeyServer;
        $url = 'https://places.googleapis.com/v1/places/' . $placeId;

        $headers = [
            'Content-Type' => 'application/json',
            'X-Goog-Api-Key' => $apiKey,
            'X-Goog-FieldMask' => 'id,displayName,formattedAddress,location',
        ];

        $response = Http::withHeaders($headers)->get($url);

        return $response->json() ?? [];
    }

    /**
     * {@inheritdoc}
     */
    public function routeMatrix(
        float $originLat,
        float $originLng,
        float $destLat,
        float $destLng
    ): array {
        // OLD GOOGLE MAPS IMPLEMENTATION (commented out, kept for reference)
        // $apiKey = $this->apiKeyServer;
        // $url = "https://routes.googleapis.com/distanceMatrix/v2:computeRouteMatrix";
        // $data = [
        //     'origins' => [
        //         ['waypoint' => ['location' => ['latLng' => ['latitude' => $originLat, 'longitude' => $originLng]]]],
        //     ],
        //     'destinations' => [
        //         ['waypoint' => ['location' => ['latLng' => ['latitude' => $destLat, 'longitude' => $destLng]]]],
        //     ],
        //     'travelMode' => 'WALK',
        // ];
        // $headers = [
        //     'Content-Type: application/json',
        //     "X-Goog-Api-Key: $apiKey",
        //     'X-Goog-FieldMask: duration,distanceMeters,localizedValues',
        // ];
        // $ch = curl_init();
        // curl_setopt($ch, CURLOPT_URL, $url);
        // curl_setopt($ch, CURLOPT_POST, true);
        // curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        // $response = curl_exec($ch);
        // curl_close($ch);
        // return json_decode($response, true)[0];

        $apiKey = $this->apiKeyServer;
        $url = 'https://routes.googleapis.com/distanceMatrix/v2:computeRouteMatrix';

        $payload = [
            'origins' => [
                ['waypoint' => ['location' => ['latLng' => ['latitude' => $originLat, 'longitude' => $originLng]]]],
            ],
            'destinations' => [
                ['waypoint' => ['location' => ['latLng' => ['latitude' => $destLat, 'longitude' => $destLng]]]],
            ],
            'travelMode' => 'WALK',
        ];

        $headers = [
            'Content-Type' => 'application/json',
            'X-Goog-Api-Key' => $apiKey,
            'X-Goog-FieldMask' => 'duration,distanceMeters,localizedValues',
        ];

        $response = Http::withHeaders($headers)->post($url, $payload);
        $json = $response->json();

        return is_array($json) && isset($json[0]) ? $json[0] : ($json ?? []);
    }

    /**
     * {@inheritdoc}
     */
    public function geocode(float $lat, float $lng, ?string $language = null): array
    {
        // OLD GOOGLE MAPS IMPLEMENTATION (commented out, kept for reference)
        // $response = Http::get('https://maps.googleapis.com/maps/api/geocode/json?latlng=' . $lat . ',' . $lng . '&key=' . $this->apiKeyServer);
        // return $response->json();

        $response = Http::get(
            'https://maps.googleapis.com/maps/api/geocode/json',
            [
                'latlng' => $lat . ',' . $lng,
                'key' => $this->apiKeyServer,
                'language' => $language ?? app()->getLocale(),
            ]
        );

        return $response->json() ?? [];
    }

    /**
     * {@inheritdoc}
     */
    public function webMapConfig(): array
    {
        $clientKey = BusinessSetting::where('key', 'map_api_key')->first()?->value
            ?? config('maps.google.api_key_client');

        $jsUrl = sprintf(
            'https://maps.googleapis.com/maps/api/js?key=%s&libraries=drawing,places&v=3.45.8',
            $clientKey
        );

        return [
            'driver' => 'google',
            'js_url' => $jsUrl,
            'default_center' => config('maps.default_center'),
        ];
    }
}
