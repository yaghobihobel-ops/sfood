<?php

namespace Tests\Unit;

use App\Models\PaymentRequest;
use App\Services\Payments\Gateways\PasargadGateway;
use App\Services\Payments\Gateways\SamanGateway;
use App\Services\Payments\PaymentGatewayFactory;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PaymentGatewayFactoryTest extends TestCase
{
    use WithFaker;

    public function test_factory_resolves_saman_gateway(): void
    {
        $paymentRequest = new PaymentRequest();
        $paymentRequest->payment_method = 'saman';

        $factory = new PaymentGatewayFactory();

        $this->assertInstanceOf(SamanGateway::class, $factory->make($paymentRequest));
    }

    public function test_factory_resolves_pasargad_gateway(): void
    {
        $paymentRequest = new PaymentRequest();
        $paymentRequest->payment_method = 'pasargad';

        $factory = new PaymentGatewayFactory();

        $this->assertInstanceOf(PasargadGateway::class, $factory->make($paymentRequest));
    }
}
