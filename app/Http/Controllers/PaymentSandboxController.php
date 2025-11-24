<?php

namespace App\Http\Controllers;

use App\Models\PaymentRequest;
use App\Traits\Processor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentSandboxController extends Controller
{
    use Processor;

    /**
     * Render a simple page to simulate success or failure for sandbox gateways.
     */
    public function prompt(Request $request, string $gateway, PaymentRequest $payment_request)
    {
        return view('payment-sandbox', [
            'gateway' => $gateway,
            'paymentRequest' => $payment_request,
        ]);
    }

    /**
     * Redirect back into the gateway callback with a sandbox_result flag.
     */
    public function result(string $gateway, PaymentRequest $payment_request, string $result): RedirectResponse
    {
        $targetRoute = match ($gateway) {
            'saman' => 'saman.callback',
            'pasargad' => 'pasargad.callback',
            default => null,
        };

        if (!$targetRoute) {
            Log::warning('Unknown sandbox gateway', ['gateway' => $gateway]);
            return $this->payment_response($payment_request, 'fail');
        }

        return redirect()->route($targetRoute, [
            'payment_id' => $payment_request->id,
            'sandbox_result' => $result,
        ]);
    }
}
