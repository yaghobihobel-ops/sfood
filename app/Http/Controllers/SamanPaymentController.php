<?php

namespace App\Http\Controllers;

use App\Models\PaymentRequest;
use App\Services\Payments\PaymentGatewayFactory;
use App\Traits\Processor;
use Illuminate\Contracts\Foundation\Application as FoundationApplication;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Throwable;

class SamanPaymentController extends Controller
{
    use Processor;

    public function __construct(private PaymentRequest $paymentRequest, private PaymentGatewayFactory $gatewayFactory)
    {
    }

    /**
     * Redirect the user to Saman (SEP) payment page.
     */
    public function pay(Request $request): RedirectResponse|JsonResponse|FoundationApplication|Redirector
    {
        $validator = Validator::make($request->all(), [
            'payment_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($this->response_formatter(GATEWAYS_DEFAULT_400, null, $this->error_processor($validator)), 400);
        }

        $payment = $this->paymentRequest::where(['id' => $request['payment_id']])->where(['is_paid' => 0])->first();
        if (!$payment) {
            return response()->json($this->response_formatter(GATEWAYS_DEFAULT_204), 200);
        }

        try {
            $gateway = $this->gatewayFactory->make($payment);
            return $gateway->pay($payment);
        } catch (Throwable $exception) {
            Log::warning('Saman pay failed', [
                'payment_id' => $payment->id,
                'error' => $exception->getMessage(),
            ]);

            return $this->payment_response($payment, 'fail');
        }
    }

    /**
     * Handle Saman callback and mark the payment accordingly.
     */
    public function callback(Request $request): RedirectResponse|JsonResponse|FoundationApplication|Redirector
    {
        $paymentId = $request->input('payment_id', $request->input('ResNum'));
        $payment = $this->paymentRequest::where(['id' => $paymentId])->first();

        if (!$payment) {
            return redirect()->route('payment-fail');
        }

        try {
            $gateway = $this->gatewayFactory->make($payment);
            $result = $gateway->verify($request, $payment);
        } catch (Throwable $exception) {
            Log::warning('Saman callback failed', [
                'payment_id' => $payment->id,
                'error' => $exception->getMessage(),
            ]);

            if (isset($payment) && function_exists($payment->failure_hook)) {
                call_user_func($payment->failure_hook, $payment);
            }

            return $this->payment_response($payment, 'fail');
        }

        if ($result['success']) {
            $payment->transaction_id = $result['transaction_id'] ?? $payment->transaction_id;
            $payment->is_paid = 1;
            $payment->save();

            if (isset($payment) && function_exists($payment->success_hook)) {
                call_user_func($payment->success_hook, $payment);
            }

            return $this->payment_response($payment, 'success');
        }

        $payment->transaction_id = $result['transaction_id'] ?? $payment->transaction_id;
        $payment->save();

        if (isset($payment) && function_exists($payment->failure_hook)) {
            call_user_func($payment->failure_hook, $payment);
        }

        return $this->payment_response($payment, 'fail');
    }
}
