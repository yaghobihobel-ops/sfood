<?php

namespace App\Services\Payments\Gateways;

use App\Models\PaymentRequest;
use App\Services\Payments\PaymentGatewayInterface;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use InvalidArgumentException;

/**
 * SamanGateway
 *
 * Skeleton implementation for SEP (Saman Bank) gateway.
 * Bank-specific SOAP/XML calls should be added where TODO notes exist
 * according to the official SEP documentation.
 */
class SamanGateway implements PaymentGatewayInterface
{
    /**
     * Redirect user to the gateway's hosted payment page.
     *
     * @param PaymentRequest $paymentRequest
     * @return RedirectResponse|ResponseFactory|Response
     */
    public function pay(PaymentRequest $paymentRequest)
    {
        $config = config('payment_gateways.saman', []);
        $this->assertConfigured($config);

        $callback = $this->callbackUrl($paymentRequest, $config);
        $endpoint = $this->purchaseEndpoint($config);

        $payload = [
            // TODO: Replace with SEP-specific token generation if required.
            'Amount' => $paymentRequest->payment_amount,
            'MID' => $config['merchant_id'] ?? null,
            'TerminalId' => $config['terminal_id'] ?? null,
            'ResNum' => $paymentRequest->id,
            'RedirectURL' => $callback,
        ];

        return response($this->buildAutoSubmitForm($endpoint, $payload));
    }

    /**
     * Verify callback response from Saman gateway.
     *
     * @param \Illuminate\Http\Request $request
     * @param PaymentRequest $paymentRequest
     * @return array{success: bool, message: string|null, transaction_id?: string|null, raw_response?: mixed}
     */
    public function verify($request, PaymentRequest $paymentRequest): array
    {
        $responseState = $request->input('State', $request->input('state'));
        $success = in_array($responseState, ['OK', 'ok', '0', 0], true);
        $transactionId = $request->input('RefNum');

        // TODO: Implement SEP verification/settlement request here based on official API.

        return [
            'success' => $success,
            'message' => $success ? null : 'Verification failed or payment declined.',
            'transaction_id' => $transactionId,
            'raw_response' => $request->all(),
        ];
    }

    /**
     * @param array $config
     * @return void
     */
    private function assertConfigured(array $config): void
    {
        if (empty($config['merchant_id']) || empty($config['terminal_id'])) {
            throw new InvalidArgumentException('Saman gateway is not configured properly.');
        }
    }

    /**
     * @param PaymentRequest $paymentRequest
     * @param array $config
     * @return string
     */
    private function callbackUrl(PaymentRequest $paymentRequest, array $config): string
    {
        return $config['callback_url'] ?? route('saman.callback', ['payment_id' => $paymentRequest->id]);
    }

    /**
     * @param array $config
     * @return string
     */
    private function purchaseEndpoint(array $config): string
    {
        return ($config['mode'] ?? 'test') === 'live'
            ? 'https://sep.shaparak.ir/Payment.aspx'
            : 'https://sandbox.sep.shaparak.ir/Payment.aspx';
    }

    /**
     * Create a minimal auto-submit HTML form to redirect to Saman gateway.
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
    <title>Saman Redirect</title>
</head>
<body onload="document.forms[0].submit()">
<p>Redirecting to Saman gateway...</p>
<form method="post" action="{$action}">
    {$formInputs}
    <noscript><button type="submit">Continue</button></noscript>
</form>
</body>
</html>
HTML;
    }
}
