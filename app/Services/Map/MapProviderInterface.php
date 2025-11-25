<?php

namespace App\Services\Map;

/**
 * MapProviderInterface
 *
 * Abstraction over map provider (Google, Neshan, ...).
 * Implementations must return data in shapes compatible with existing API responses.
 */
interface MapProviderInterface
{
    /**
     * Place autocomplete, similar to Google Places Autocomplete.
     *
     * @param string $input
     * @param string|null $language
     * @return array Raw data compatible with current /config/place-api-autocomplete JSON
     */
    public function autocomplete(string $input, ?string $language = null): array;

    /**
     * Place details, similar to Google Places Place Details.
     *
     * @param string $placeId
     * @param string|null $language
     * @return array Raw data compatible with current /config/place-api-details JSON
     */
    public function placeDetails(string $placeId, ?string $language = null): array;

    /**
     * Distance/route matrix between origin and destination.
     * Should roughly match current distance_api output
     * (duration, distanceMeters, localizedValues).
     */
    public function routeMatrix(
        float $originLat,
        float $originLng,
        float $destLat,
        float $destLng
    ): array;

    /**
     * Reverse geocoding (lat,lng -> address).
     * Should roughly match current geocode_api JSON shape.
     */
    public function geocode(
        float $lat,
        float $lng,
        ?string $language = null
    ): array;

    /**
     * Web map config for Blade views (tile url, api key, initial center, zoom, etc.).
     */
    public function webMapConfig(): array;
}

