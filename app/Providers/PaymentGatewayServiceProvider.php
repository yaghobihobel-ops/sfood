<?php

namespace App\Providers;

use App\Services\Payments\PaymentGatewayFactory;
use Illuminate\Support\ServiceProvider;

/**
 * PaymentGatewayServiceProvider
 *
 * Registers the payment gateway factory abstraction in the container.
 */
class PaymentGatewayServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register()
    {
        $this->app->singleton(PaymentGatewayFactory::class, function () {
            return new PaymentGatewayFactory();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot()
    {
        //
    }
}
