<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Services\Payments\GatewayConfigResolver;
use App\Services\Payments\PaymentGatewayManager;
use App\Services\Payments\PaymentStateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentWebhookController extends Controller
{
    public function handleWebhook(
        Request $request,
        string $provider,
        GatewayConfigResolver $configResolver,
        PaymentGatewayManager $gatewayManager,
        PaymentStateService $paymentStateService
    ): JsonResponse {
        $payment = $this->resolvePaymentFromWebhookRequest($request);

        if (! $payment) {
            return response()->json([
                'message' => 'Payment not found.',
            ], 404);
        }

        if ($payment->provider !== $provider) {
            return response()->json([
                'message' => 'Payment not found.',
            ], 404);
        }

        if (Payment::isFinalStatus($payment->status)) {
            return response()->json([
                'status' => 'ignored',
                'payment_status' => $payment->status,
            ]);
        }

        try {
            $gatewayConfig = $configResolver->resolve($payment->provider, $payment->method, false);
            $gateway = $gatewayManager->getGateway($provider);
            $verifyResult = $gateway->verifyWebhook($request, $gatewayConfig);

            if (! $verifyResult->valid) {
                Log::warning('Payment webhook verification failed', [
                    'payment_id' => $payment->id,
                    'provider' => $provider,
                    'error' => $verifyResult->error,
                ]);

                return response()->json([
                    'message' => 'Webhook verification failed.',
                ], 422);
            }

            $payment = $paymentStateService->applyVerificationResult($payment, $verifyResult);

            return response()->json([
                'status' => 'ok',
                'payment_status' => $payment->status,
            ]);
        } catch (\Throwable $e) {
            Log::error('Payment webhook exception', [
                'payment_id' => $payment->id,
                'provider' => $provider,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Webhook processing failed.',
            ], 500);
        }
    }

    private function resolvePaymentFromWebhookRequest(Request $request): ?Payment
    {
        $paymentId = (int) ($request->input('payment_id') ?? $request->query('payment_id'));

        if ($paymentId > 0) {
            return Payment::query()->with('order')->find($paymentId);
        }

        $externalOrderId = (string) $request->input('external_order_id', '');
        if ($externalOrderId !== '' && ctype_digit($externalOrderId)) {
            return Payment::query()->with('order')->find((int) $externalOrderId);
        }

        return null;
    }
}
