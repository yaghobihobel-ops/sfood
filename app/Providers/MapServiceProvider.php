<?php

namespace App\Providers;

use App\Services\Map\GoogleMapProvider;
use App\Services\Map\MapProviderInterface;
use App\Services\Map\MapService;
use Illuminate\Support\ServiceProvider;
use Modules\Iran\Services\IranMapProvider;

/**
 * MapServiceProvider
 *
 * Registers the map provider implementation and exposes MapService.
 */
class MapServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(MapProviderInterface::class, function () {
            $driver = config('maps.driver', 'google');

            // Default to Google if the Iran module is unavailable
            return match ($driver) {
                'iran' => app(IranMapProvider::class),
                default => app(GoogleMapProvider::class),
            };
        });

        $this->app->singleton(MapService::class, function ($app) {
            return new MapService($app->make(MapProviderInterface::class));
        });
    }

    public function boot()
    {
        //
    }
}

