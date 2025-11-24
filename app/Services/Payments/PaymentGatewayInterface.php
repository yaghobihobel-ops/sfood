<?php

namespace App\Services\Payments;

use App\Models\PaymentRequest;
use Illuminate\Http\Request;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;

/**
 * PaymentGatewayInterface
 *
 * Abstraction over online payment gateways (Stripe, Paypal, Saman, Pasargad, ...).
 * Implementations must use PaymentRequest as the main data source and
 * trigger appropriate redirects or verification logic.
 */
interface PaymentGatewayInterface
{
    /**
     * Redirect user to the gateway's payment page.
     *
     * @param \App\Models\PaymentRequest $paymentRequest
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function pay(PaymentRequest $paymentRequest): RedirectResponse|ResponseFactory|Response;

    /**
     * Handle callback/verify from the gateway.
     *
     * Should mark the PaymentRequest as paid/failed and
     * return a boolean or result object describing the outcome.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\PaymentRequest $paymentRequest
     * @return array{success: bool, message: string|null, transaction_id?: string|null, raw_response?: mixed}
     */
    public function verify(Request $request, PaymentRequest $paymentRequest): array;
}
