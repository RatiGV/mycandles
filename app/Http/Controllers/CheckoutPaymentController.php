<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Payment;
use App\Models\Product;
use App\Services\Payments\GatewayConfigResolver;
use App\Services\Payments\PaymentGatewayManager;
use App\Services\Payments\Support\PayloadSanitizer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class CheckoutPaymentController extends Controller
{
    public function initiate(
        Request $request,
        GatewayConfigResolver $configResolver,
        PaymentGatewayManager $gatewayManager
    ): JsonResponse {
        $allowedMethods = collect($configResolver->activeCheckoutMethods())
            ->pluck('key')
            ->values()
            ->all();

        $validated = $request->validate([
            'payment_method' => ['required', Rule::in($allowedMethods)],
            'delivery' => ['required', 'in:1,2'],
            'district_id' => ['nullable', 'required_if:delivery,1', 'integer'],
            'address' => ['nullable', 'required_if:delivery,1', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'installment_selected' => ['nullable', 'string'],
            'installment_month' => ['nullable', 'integer', 'min:1'],
            'installment_loan_type' => ['nullable', 'string', 'max:50'],
            'bog_type' => ['nullable', 'string', 'max:50'],
        ]);

        $gatewayConfig = $configResolver->resolveByName($validated['payment_method']);
        $methodInfo = [
            'provider' => $gatewayConfig['provider'],
            'method' => $gatewayConfig['method'],
        ];
        $cart = \Cart::getContent();

        if ($cart->isEmpty()) {
            throw ValidationException::withMessages([
                'cart' => ['Cart is empty.'],
            ]);
        }

        $cartSnapshot = $this->buildCartSnapshot($cart);

        $existingPayment = $this->findFreshPendingPayment(
            userId: (int) $request->user()->id,
            provider: $methodInfo['provider'],
            method: $methodInfo['method'],
            amount: $cartSnapshot['total']
        );

        if ($existingPayment && $existingPayment->redirect_url) {
            return response()->json([
                'redirect_url' => $existingPayment->redirect_url,
                'payment_id' => $existingPayment->id,
                'gateway_order_id' => $existingPayment->gateway_payment_id,
                'reused' => true,
            ]);
        }

        $payment = DB::transaction(function () use ($request, $validated, $cartSnapshot, $methodInfo) {
            $order = $this->createOrder($request->user()->id, $validated, $cartSnapshot);

            foreach ($cartSnapshot['lines'] as $line) {
                OrderProduct::create([
                    'order_id' => $order->id,
                    'product_id' => $line['product_id'],
                    'qty' => $line['qty'],
                    'unit_price' => $line['unit_price'],
                    'price' => $line['line_total'],
                ]);
            }

            return Payment::create([
                'order_id' => $order->id,
                'provider' => $methodInfo['provider'],
                'method' => $methodInfo['method'],
                'amount' => $cartSnapshot['total'],
                'currency' => 'GEL',
                'status' => Payment::STATUS_PENDING,
                'raw_request' => PayloadSanitizer::sanitize([
                    'payment_method' => $validated['payment_method'],
                    'delivery' => $validated['delivery'],
                    'district_id' => $validated['district_id'] ?? null,
                    'address' => $validated['address'] ?? null,
                    'phone' => $validated['phone'],
                    'installment_selected' => $this->decodeInstallmentSelection($validated['installment_selected'] ?? null),
                    'installment_month' => $validated['installment_month'] ?? null,
                    'installment_loan_type' => $validated['installment_loan_type'] ?? null,
                    'bog_type' => $validated['bog_type'] ?? null,
                ]),
            ]);
        });

        try {
            $gatewayConfig['return_url'] = route('payments.return', [
                'provider' => $methodInfo['provider'],
                'payment_id' => $payment->id,
            ]);
            $gatewayConfig['callback_url'] = route('payments.webhook', [
                'provider' => $methodInfo['provider'],
                'payment_id' => $payment->id,
            ]);

            $gateway = $gatewayManager->getGateway($methodInfo['provider']);

            $result = $gateway->createPayment($payment->fresh('order', 'order.products', 'order.user'), $gatewayConfig);


            if (! $result->success) {
                $payment->status = Payment::STATUS_FAILED;
                $payment->failed_at = now();
                $payment->raw_response = PayloadSanitizer::sanitize($result->raw);
                $payment->save();

                Log::warning('Payment initiation failed', [
                    'payment_id' => $payment->id,
                    'provider' => $payment->provider,
                    'method' => $payment->method,
                    'error' => $result->error,
                ]);

                $message = 'Unable to start payment. Please try again.';

                if (config('app.debug') && ! empty($result->error)) {
                    $message = $result->error;
                }

                return response()->json([
                    'message' => $message,
                ], 422);
            }

            $payment->gateway_payment_id = $result->gatewayPaymentId;
            $payment->gateway_order_id = $result->gatewayPaymentId;
            $payment->redirect_url = $result->redirectUrl;
            $payment->raw_response = PayloadSanitizer::sanitize($result->raw);
            $payment->save();

            return response()->json([
                'redirect_url' => $result->redirectUrl,
                'payment_id' => $payment->id,
                'gateway_order_id' => $result->gatewayPaymentId,
                'reused' => false,
            ]);
        } catch (\Throwable $e) {
            Log::error('Payment initiation exception', [
                'payment_id' => $payment->id,
                'provider' => $payment->provider,
                'error' => $e->getMessage(),
            ]);

            $payment->status = Payment::STATUS_FAILED;
            $payment->failed_at = now();
            $payment->raw_response = ['error' => $e->getMessage()];
            $payment->save();

            return response()->json([
                'message' => 'Unable to start payment. Please try again later.',
            ], 500);
        }
    }

    private function buildCartSnapshot($cart): array
    {
        $productIds = $cart->pluck('id')->map(fn ($id) => (int) $id)->values()->all();
        $products = Product::query()->whereIn('id', $productIds)->get()->keyBy('id');

        $lines = [];
        $total = 0.0;

        foreach ($cart as $item) {
            $productId = (int) $item->id;
            $qty = max(1, (int) $item->quantity);
            $product = $products->get($productId);

            if (! $product || ! (int) $product->status || ! (int) $product->available) {
                throw ValidationException::withMessages([
                    'cart' => ['One or more products are unavailable. Please refresh your cart.'],
                ]);
            }

            $unitPrice = (float) $product->price;
            $lineTotal = $unitPrice * $qty;
            $total += $lineTotal;

            $lines[] = [
                'product_id' => $productId,
                'qty' => $qty,
                'unit_price' => $unitPrice,
                'line_total' => $lineTotal,
            ];
        }

        if ($total <= 0) {
            throw ValidationException::withMessages([
                'cart' => ['Order amount must be greater than zero.'],
            ]);
        }

        return [
            'lines' => $lines,
            'total' => round($total, 2),
        ];
    }

    private function createOrder(int $userId, array $validated, array $cartSnapshot): Order
    {
        $isCourier = $validated['delivery'] === '1';

        return Order::create([
            'district_id' => $isCourier ? (int) ($validated['district_id'] ?? 0) : null,
            'user_id' => $userId,
            'payment_type' => 1,
            'code' => $this->generateOrderCode(),
            'sale_code' => null,
            'sale_percent' => null,
            'transaction_id' => null,
            'pay_status' => 0,
            'status' => 1,
            'address' => $isCourier ? (string) $validated['address'] : 'Office pickup',
            'delivery' => $isCourier ? 1 : 0,
            'delivery_price' => 0,
            'total' => $cartSnapshot['total'],
            'year' => (int) now()->format('Y'),
            'month' => (int) now()->format('m'),
            'day' => (int) now()->format('d'),
        ]);
    }

    private function findFreshPendingPayment(int $userId, string $provider, string $method, float $amount): ?Payment
    {
        return Payment::query()
            ->where('provider', $provider)
            ->where('method', $method)
            ->where('status', Payment::STATUS_PENDING)
            ->where('amount', $amount)
            ->whereNotNull('redirect_url')
            ->where('created_at', '>=', now()->subMinutes(15))
            ->whereHas('order', function ($query) use ($userId) {
                $query->where('user_id', $userId)->where('pay_status', '<>', 4);
            })
            ->latest('id')
            ->first();
    }

    private function generateOrderCode(): string
    {
        do {
            $candidate = 'ORD-' . now()->format('YmdHis') . '-' . random_int(1000, 9999);
        } while (Order::query()->where('code', $candidate)->exists());

        return $candidate;
    }

    private function decodeInstallmentSelection(?string $payload): ?array
    {
        if (! is_string($payload) || trim($payload) === '') {
            return null;
        }

        $decoded = json_decode($payload, true);

        if (json_last_error() !== JSON_ERROR_NONE || ! is_array($decoded)) {
            return null;
        }

        return $decoded;
    }
}
