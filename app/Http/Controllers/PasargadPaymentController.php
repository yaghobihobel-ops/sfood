<?php

namespace App\Http\Controllers;

use App\Models\PaymentRequest;
use App\Services\Payments\PaymentGatewayFactory;
use App\Traits\Processor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use InvalidArgumentException;

/**
 * PasargadPaymentController
 *
 * Handles redirect and callback flows for the Pasargad (PEP) gateway
 * via the PaymentGatewayInterface abstraction.
 */
class PasargadPaymentController extends Controller
{
    use Processor;

    /**
     * @var PaymentRequest
     */
    private PaymentRequest $payment;

    /**
     * @var PaymentGatewayFactory
     */
    private PaymentGatewayFactory $gatewayFactory;

    public function __construct(PaymentRequest $payment, PaymentGatewayFactory $gatewayFactory)
    {
        $this->payment = $payment;
        $this->gatewayFactory = $gatewayFactory;
    }

    /**
     * Redirect to Pasargad gateway using the abstraction layer.
     */
    public function pay(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'payment_id' => 'required|uuid',
        ]);

        if ($validator->fails()) {
            return response()->json($this->response_formatter(GATEWAYS_DEFAULT_400, null, $this->error_processor($validator)), 400);
        }

        $data = $this->payment::where(['id' => $request['payment_id']])->where(['is_paid' => 0])->first();
        if (!isset($data)) {
            return response()->json($this->response_formatter(GATEWAYS_DEFAULT_204), 200);
        }

        try {
            $gateway = $this->gatewayFactory->make($data);
            return $gateway->pay($data);
        } catch (InvalidArgumentException $exception) {
            Log::warning('Pasargad gateway is not available', ['message' => $exception->getMessage()]);
        } catch (\Throwable $exception) {
            Log::error('Pasargad payment initiation failed', ['message' => $exception->getMessage()]);
        }

        if (isset($data) && function_exists($data->failure_hook)) {
            call_user_func($data->failure_hook, $data);
        }

        return $this->payment_response($data, 'fail');
    }

    /**
     * Handle Pasargad callback and verification.
     */
    public function callback(Request $request): JsonResponse|\Illuminate\Contracts\Foundation\Application|\Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
    {
        $paymentId = $request->get('payment_id', $request->get('invoiceNumber'));
        $data = $this->payment::where(['id' => $paymentId])->first();

        if (!$data) {
            return response()->json($this->response_formatter(GATEWAYS_DEFAULT_404), 404);
        }

        try {
            $gateway = $this->gatewayFactory->make($data);
            $result = $gateway->verify($request, $data);
        } catch (InvalidArgumentException $exception) {
            Log::warning('Pasargad callback rejected', ['message' => $exception->getMessage()]);
            $result = ['success' => false, 'message' => $exception->getMessage()];
        } catch (\Throwable $exception) {
            Log::error('Pasargad verification failed', ['message' => $exception->getMessage()]);
            $result = ['success' => false, 'message' => $exception->getMessage()];
        }

        if ($result['success']) {
            $this->payment::where(['id' => $data->id])->update([
                'payment_method' => 'pasargad',
                'is_paid' => 1,
                'transaction_id' => $result['transaction_id'] ?? null,
            ]);

            $data = $this->payment::where(['id' => $data->id])->first();
            if (isset($data) && function_exists($data->success_hook)) {
                call_user_func($data->success_hook, $data);
            }

            return $this->payment_response($data, 'success');
        }

        $paymentData = $this->payment::where(['id' => $data->id])->first();
        if (isset($paymentData) && function_exists($paymentData->failure_hook)) {
            call_user_func($paymentData->failure_hook, $paymentData);
        }

        return $this->payment_response($paymentData, 'fail');
    }
}
