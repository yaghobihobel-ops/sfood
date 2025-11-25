<?php

namespace App\Services\Payments;

use App\Models\PaymentRequest;
use App\Services\Payments\Gateways\PasargadGateway;
use App\Services\Payments\Gateways\SamanGateway;
use InvalidArgumentException;

/**
 * PaymentGatewayFactory
 *
 * Resolve proper PaymentGatewayInterface implementation
 * based on PaymentRequest->payment_method.
 */
class PaymentGatewayFactory
{
    /**
     * Resolve the gateway implementation for the given payment request.
     *
     * @param \App\Models\PaymentRequest $paymentRequest
     * @return \App\Services\Payments\PaymentGatewayInterface
     */
    public function make(PaymentRequest $paymentRequest): PaymentGatewayInterface
    {
        $method = $paymentRequest->payment_method;

        return match ($method) {
            'saman' => app(SamanGateway::class),
            'pasargad' => app(PasargadGateway::class),
            // Additional gateways can be wired here incrementally without affecting existing flows.
            default => throw new InvalidArgumentException("Unsupported payment method: {$method}"),
        };
    }
}
