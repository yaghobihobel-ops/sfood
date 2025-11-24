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
 * PasargadGateway
 *
 * Handles Pasargad (PEP) redirection and verification with a thin abstraction.
 * Bank-specific signing and verification calls are left as TODO markers for
 * future integration with official SDKs or direct API calls.
 */
class PasargadGateway implements PaymentGatewayInterface
{
    /**
     * Redirect user to Pasargad hosted payment form.
     *
     * @param \App\Models\PaymentRequest $paymentRequest
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function pay(PaymentRequest $paymentRequest): RedirectResponse|ResponseFactory|Response
    {
        $config = config('payment_gateways.pasargad', []);
        $callbackUrl = $config['callback_url'] ?? url('payment/pasargad/callback');

        if (!empty($config['sandbox'])) {
            return redirect()->route('payment.sandbox.prompt', [
                'gateway' => 'pasargad',
                'payment_request' => $paymentRequest->id,
            ]);
        }

        // Pasargad typically requires amount in Rial.
        $amountRial = $paymentRequest->payment_amount;
        if (($config['currency_multiplier'] ?? null) === 'toman_to_rial') {
            $amountRial = $paymentRequest->payment_amount * 10;
        }

        // OLD IMPLEMENTATION (kept for reference, replaced by new Saman/Pasargad abstraction)
        // A real implementation would build the signed data string and fetch a token via PEP API.
        $token = Str::uuid()->toString();

        $payload = [
            'invoiceNumber' => $paymentRequest->id,
            'amount' => $amountRial,
            'merchantCode' => $config['merchant_code'] ?? null,
            'terminalCode' => $config['terminal_code'] ?? null,
            'redirectAddress' => $callbackUrl,
            'timeStamp' => now()->toIso8601String(),
            'token' => $token,
        ];

        Log::info('Pasargad gateway initialized', [
            'payment_request' => $paymentRequest->id,
            'payload' => $payload,
        ]);

        $redirectUrl = $config['payment_url'] ?? 'https://pep.shaparak.ir/payment.aspx';
        $form = view('components.payment-gateway-redirect', [
            'action' => $redirectUrl,
            'fields' => $payload,
        ]);

        return response($form);
    }

    /**
     * Verify Pasargad callback.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\PaymentRequest $paymentRequest
     * @return array{success: bool, message: string|null, transaction_id?: string|null, raw_response?: mixed}
     */
    public function verify(Request $request, PaymentRequest $paymentRequest): array
    {
        $config = config('payment_gateways.pasargad', []);

        if (!empty($config['sandbox'])) {
            $sandboxResult = $request->input('sandbox_result', 'success');
            $transactionId = 'SANDBOX-PASARGAD-' . $paymentRequest->id;

            return [
                'success' => $sandboxResult === 'success',
                'message' => $sandboxResult === 'success' ? null : 'Sandbox failure simulated',
                'transaction_id' => $transactionId,
                'raw_response' => $request->all(),
            ];
        }

        $token = $request->input('tref');

        // TODO: Implement Pasargad verify/settle call using official signing method with merchant certificate.
        $isSuccessful = !empty($token);

        $message = $isSuccessful ? null : ($request->input('resultMessage') ?? 'Payment verification failed');

        Log::info('Pasargad gateway callback received', [
            'payment_request' => $paymentRequest->id,
            'token' => $token,
            'raw' => $request->all(),
        ]);

        return [
            'success' => $isSuccessful,
            'message' => $message,
            'transaction_id' => $token,
            'raw_response' => $request->all(),
        ];
    }
}
