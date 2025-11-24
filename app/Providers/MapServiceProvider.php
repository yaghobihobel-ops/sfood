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
 * Registers the configured map provider implementation for dependency injection.
 */
class MapServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(MapProviderInterface::class, function () {
            $driver = config('maps.driver', 'google');

            // NOTE: make sure Modules\Iran exists; otherwise default to Google
            return match ($driver) {
                'iran', 'neshan' => app(IranMapProvider::class),
                default => app(GoogleMapProvider::class),
            };
        });

        $this->app->singleton(MapService::class, function ($app) {
            return new MapService($app->make(MapProviderInterface::class));
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
