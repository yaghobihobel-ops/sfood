<?php

namespace App\Services\Payments;

use App\Models\PaymentRequest;

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
    public function pay(PaymentRequest $paymentRequest);

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
    public function verify($request, PaymentRequest $paymentRequest): array;
}
