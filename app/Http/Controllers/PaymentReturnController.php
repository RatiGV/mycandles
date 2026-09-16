<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Services\Payments\GatewayConfigResolver;
use App\Services\Payments\PaymentGatewayManager;
use App\Services\Payments\PaymentStateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentReturnController extends Controller
{
    public function handleReturn(
        Request $request,
        string $provider,
        GatewayConfigResolver $configResolver,
        PaymentGatewayManager $gatewayManager,
        PaymentStateService $paymentStateService
    ) {
        $paymentId = (int) $request->query('payment_id');

        if (! $paymentId) {
            return response()->view('payments.failed', [
                'message' => 'Payment reference is missing.',
            ], 422);
        }

        $payment = Payment::query()->with('order')->find($paymentId);

        if (! $payment || $payment->provider !== $provider) {
            return response()->view('payments.failed', [
                'message' => 'Payment could not be found.',
            ], 404);
        }

        if ($payment->status === Payment::STATUS_PAID) {
            return view('payments.success', compact('payment'));
        }

        try {
            if (! $request->query('gateway_payment_id') && $payment->gateway_payment_id) {
                $request->merge(['gateway_payment_id' => $payment->gateway_payment_id]);
            }

            $gatewayConfig = $configResolver->resolve($payment->provider, $payment->method, false);
            $gateway = $gatewayManager->getGateway($provider);
            $verifyResult = $gateway->verifyReturn($request, $gatewayConfig);

            if (! $verifyResult->valid) {
                Log::warning('Payment return verification failed', [
                    'payment_id' => $payment->id,
                    'provider' => $provider,
                    'error' => $verifyResult->error,
                ]);

                return response()->view('payments.failed', [
                    'payment' => $payment,
                    'message' => 'Payment verification failed. Please contact support if funds were charged.',
                ], 422);
            }

            $payment = $paymentStateService->applyVerificationResult($payment, $verifyResult);

            if ($payment->status === Payment::STATUS_PAID) {
                if (auth()->check() && (int) $payment->order->user_id === (int) auth()->id()) {
                    \Cart::clear();
                }

                return view('payments.success', compact('payment'));
            }

            return view('payments.failed', [
                'payment' => $payment,
                'message' => 'Payment was not completed successfully.',
            ]);
        } catch (\Throwable $e) {
            Log::error('Payment return exception', [
                'payment_id' => $payment->id,
                'provider' => $provider,
                'error' => $e->getMessage(),
            ]);

            return response()->view('payments.failed', [
                'payment' => $payment,
                'message' => 'Payment verification failed unexpectedly. Please contact support.',
            ], 500);
        }
    }
}
