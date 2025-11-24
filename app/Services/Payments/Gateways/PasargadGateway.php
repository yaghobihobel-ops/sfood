<?php

namespace App\Services\Payments\Gateways;

use App\Models\PaymentRequest;
use App\Services\Payments\PaymentGatewayInterface;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use InvalidArgumentException;

/**
 * PasargadGateway
 *
 * Skeleton integration for PEP (Pasargad Bank) gateway.
 * Cryptographic signing and verification must follow official Pasargad docs.
 */
class PasargadGateway implements PaymentGatewayInterface
{
    /**
     * Redirect user to Pasargad hosted payment page.
     *
     * @param PaymentRequest $paymentRequest
     * @return RedirectResponse|ResponseFactory|Response
     */
    public function pay(PaymentRequest $paymentRequest)
    {
        $config = config('payment_gateways.pasargad', []);
        $this->assertConfigured($config);

        $callback = $this->callbackUrl($paymentRequest, $config);
        $endpoint = $this->purchaseEndpoint($config);

        $invoiceDate = now()->format('Y/m/d H:i:s');
        $payload = [
            // TODO: Add RSA signature using cert_path and invoice details per Pasargad API.
            'amount' => $this->normalizeAmount($paymentRequest->payment_amount),
            'merchantCode' => $config['merchant_code'] ?? null,
            'terminalCode' => $config['terminal_code'] ?? null,
            'invoiceNumber' => $paymentRequest->id,
            'invoiceDate' => $invoiceDate,
            'redirectAddress' => $callback,
        ];

        return response($this->buildAutoSubmitForm($endpoint, $payload));
    }

    /**
     * Verify callback payload from Pasargad gateway.
     *
     * @param \Illuminate\Http\Request $request
     * @param PaymentRequest $paymentRequest
     * @return array{success: bool, message: string|null, transaction_id?: string|null, raw_response?: mixed}
     */
    public function verify($request, PaymentRequest $paymentRequest): array
    {
        $transactionId = $request->input('tref', $request->input('referenceId'));
        $successFlag = $request->input('result') ?? $request->input('Result');
        $success = in_array($successFlag, ['Success', 'success', '0', 0, true], true);

        // TODO: Call Pasargad verification endpoint using RSA signature and confirm transaction status.

        return [
            'success' => (bool) $success,
            'message' => $success ? null : 'Verification failed or payment declined.',
            'transaction_id' => $transactionId,
            'raw_response' => $request->all(),
        ];
    }

    /**
     * Normalize amount to Rial (Pasargad often expects value * 10 when using Toman in UI).
     *
     * @param float|int $amount
     * @return float|int
     */
    private function normalizeAmount($amount)
    {
        return $amount;
    }

    /**
     * @param array $config
     * @return void
     */
    private function assertConfigured(array $config): void
    {
        if (empty($config['merchant_code']) || empty($config['terminal_code'])) {
            throw new InvalidArgumentException('Pasargad gateway is not configured properly.');
        }
    }

    /**
     * @param PaymentRequest $paymentRequest
     * @param array $config
     * @return string
     */
    private function callbackUrl(PaymentRequest $paymentRequest, array $config): string
    {
        return $config['callback_url'] ?? route('pasargad.callback', ['payment_id' => $paymentRequest->id]);
    }

    /**
     * @param array $config
     * @return string
     */
    private function purchaseEndpoint(array $config): string
    {
        return ($config['mode'] ?? 'test') === 'live'
            ? 'https://pep.shaparak.ir/gateway.aspx'
            : 'https://pep.shaparak.ir/gateway.aspx';
    }

    /**
     * Create HTML that auto-submits to Pasargad gateway.
     *
     * @param string $action
     * @param array $fields
     * @return string
     */
    private function buildAutoSubmitForm(string $action, array $fields): string
    {
        $formInputs = collect($fields)
            ->filter()
            ->map(fn ($value, $name) => '<input type="hidden" name="' . e($name) . '" value="' . e($value) . '">')
            ->implode('');

        return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Pasargad Redirect</title>
</head>
<body onload="document.forms[0].submit()">
<p>Redirecting to Pasargad gateway...</p>
<form method="post" action="{$action}">
    {$formInputs}
    <noscript><button type="submit">Continue</button></noscript>
</form>
</body>
</html>
HTML;
    }
}
