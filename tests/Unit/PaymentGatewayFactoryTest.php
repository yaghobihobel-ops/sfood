<?php

namespace Tests\Unit;

use App\Models\PaymentRequest;
use App\Services\Payments\Gateways\PasargadGateway;
use App\Services\Payments\Gateways\SamanGateway;
use App\Services\Payments\PaymentGatewayFactory;
use InvalidArgumentException;
use Tests\TestCase;

class PaymentGatewayFactoryTest extends TestCase
{
    public function test_it_resolves_saman_gateway(): void
    {
        $paymentRequest = new PaymentRequest();
        $paymentRequest->payment_method = 'saman';

        $factory = app(PaymentGatewayFactory::class);

        $gateway = $factory->make($paymentRequest);

        $this->assertInstanceOf(SamanGateway::class, $gateway);
    }

    public function test_it_resolves_pasargad_gateway(): void
    {
        $paymentRequest = new PaymentRequest();
        $paymentRequest->payment_method = 'pasargad';

        $factory = app(PaymentGatewayFactory::class);

        $gateway = $factory->make($paymentRequest);

        $this->assertInstanceOf(PasargadGateway::class, $gateway);
    }

    public function test_it_rejects_unknown_gateway(): void
    {
        $paymentRequest = new PaymentRequest();
        $paymentRequest->payment_method = 'unknown-gateway';

        $factory = app(PaymentGatewayFactory::class);

        $this->expectException(InvalidArgumentException::class);
        $factory->make($paymentRequest);
    }
}
