<?php

namespace App\Providers;

use App\Services\Payments\PaymentGatewayFactory;
use Illuminate\Support\ServiceProvider;

class PaymentGatewayServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton(PaymentGatewayFactory::class, function ($app) {
            return new PaymentGatewayFactory();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(): void
    {
        // No boot logic required for now. Factory is resolved via the container.
    }
}
