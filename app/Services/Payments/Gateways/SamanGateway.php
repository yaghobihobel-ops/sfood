<?php

namespace App\Services\Payments\Gateways;

use App\Models\PaymentRequest;
use App\Services\Payments\PaymentGatewayInterface;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * SamanGateway
 *
 * Handles redirection and verification for Saman (SEP) bank gateway.
 * The low-level HTTP calls are left as TODOs with guidance for integrating
 * official SEP APIs without breaking existing payment flows.
 */
class SamanGateway implements PaymentGatewayInterface
{
    /**
     * Initiate the payment by redirecting the user to Saman's hosted page.
     *
     * @param \App\Models\PaymentRequest $paymentRequest
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function pay(PaymentRequest $paymentRequest): RedirectResponse|ResponseFactory|Response
    {
        $config = config('payment_gateways.saman', []);
        $callbackUrl = $config['callback_url'] ?? url('payment/saman/callback');

        if (!empty($config['sandbox'])) {
            return redirect()->route('payment.sandbox.prompt', [
                'gateway' => 'saman',
                'payment_request' => $paymentRequest->id,
            ]);
        }

        // TODO: Replace this placeholder token generation with the official Saman token API call.
        // OLD IMPLEMENTATION (kept for reference, replaced by new Saman/Pasargad abstraction)
        // Direct cURL requests to SEP would be placed here once credentials and endpoints are finalized.
        $token = Str::uuid()->toString();

        $redirectUrl = $config['payment_url'] ?? 'https://sep.shaparak.ir/payment.aspx';

        $payload = [
            'Amount' => $paymentRequest->payment_amount,
            'MerchantID' => $config['merchant_id'] ?? null,
            'ResNum' => $paymentRequest->id,
            'RedirectURL' => $callbackUrl,
            'Token' => $token,
        ];

        Log::info('Saman gateway initialized', [
            'payment_request' => $paymentRequest->id,
            'payload' => $payload,
        ]);

        // Most Iranian gateways require an auto-submitting form POST.
        $form = view('components.payment-gateway-redirect', [
            'action' => $redirectUrl,
            'fields' => $payload,
        ]);

        return response($form);
    }

    /**
     * Verify the callback from Saman and return a normalized result array.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\PaymentRequest $paymentRequest
     * @return array{success: bool, message: string|null, transaction_id?: string|null, raw_response?: mixed}
     */
    public function verify(Request $request, PaymentRequest $paymentRequest): array
    {
        $config = config('payment_gateways.saman', []);

        if (!empty($config['sandbox'])) {
            $sandboxResult = $request->input('sandbox_result', 'success');
            $transactionId = 'SANDBOX-SAMAN-' . $paymentRequest->id;

            return [
                'success' => $sandboxResult === 'success',
                'message' => $sandboxResult === 'success' ? null : 'Sandbox failure simulated',
                'transaction_id' => $transactionId,
                'raw_response' => $request->all(),
            ];
        }

        $state = $request->input('State');
        $referenceNumber = $request->input('RefNum');

        // TODO: Implement server-to-server verification with SEP settlement API.
        $isSuccessful = $state === 'OK' || $state === 'success';

        $message = $isSuccessful ? null : ($request->input('Message') ?? 'Payment verification failed');

        Log::info('Saman gateway callback received', [
            'payment_request' => $paymentRequest->id,
            'state' => $state,
            'ref_num' => $referenceNumber,
            'raw' => $request->all(),
        ]);

        return [
            'success' => $isSuccessful,
            'message' => $message,
            'transaction_id' => $referenceNumber,
            'raw_response' => $request->all(),
        ];
    }
}
