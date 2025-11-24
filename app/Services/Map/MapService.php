<?php

namespace App\Services\Map;

use Illuminate\Support\Facades\App;

/**
 * MapService
 *
 * Thin wrapper around MapProviderInterface used by controllers/traits.
 */
class MapService
{
    protected MapProviderInterface $provider;

    public function __construct(MapProviderInterface $provider)
    {
        $this->provider = $provider;
    }

    public function autocomplete(string $input): array
    {
        return $this->provider->autocomplete($input, App::getLocale());
    }

    public function placeDetails(string $placeId): array
    {
        return $this->provider->placeDetails($placeId, App::getLocale());
    }

    public function distance(
        float $originLat,
        float $originLng,
        float $destLat,
        float $destLng
    ): array {
        return $this->provider->routeMatrix($originLat, $originLng, $destLat, $destLng);
    }

    public function geocode(float $lat, float $lng): array
    {
        return $this->provider->geocode($lat, $lng, App::getLocale());
    }

    public function webConfig(): array
    {
        return $this->provider->webMapConfig();
    }
}
