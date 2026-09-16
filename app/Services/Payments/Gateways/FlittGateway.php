<?php

namespace App\Services\Payments\Gateways;

use App\Http\Controllers\Signature;
use App\Models\Payment;
use App\Services\Payments\DTO\StatusResult;
use App\Services\Payments\DTO\VerifyResult;
use App\Services\Payments\Support\PayloadSanitizer;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use RuntimeException;

class FlittGateway extends AbstractGateway
{
    protected function providerName(): string
    {
        return Payment::PROVIDER_FLITT;
    }

    protected function configBaseUrlKey(): string
    {
        return 'services.payments.flitt_base_url';
    }

    protected function defaultCreateEndpoint(): string
    {
        return '/api/checkout/url';
    }

    protected function defaultStatusEndpoint(): string
    {
        return '/api/recurring/status/{id}';
    }

    public function verifyReturn(Request $request, array $gatewayConfig): VerifyResult
    {
        $payload = $this->unwrapPayload($request->query());
        $sanitizedPayload = PayloadSanitizer::sanitize($payload);

        if (! $this->verifySignature($request, $gatewayConfig, false)) {
            return VerifyResult::invalid('Invalid Flitt return signature.', $sanitizedPayload);
        }

        $gatewayPaymentId = $this->extractGatewayPaymentId($payload)
            ?? (string) ($request->query('order_id') ?? '');
        $gatewayPaymentId = $gatewayPaymentId !== '' ? $gatewayPaymentId : null;

        $status = $this->extractStatus($payload);
        if ($status === Payment::STATUS_PENDING) {
            return VerifyResult::invalid(
                'Unable to determine Flitt payment status from return payload.',
                $sanitizedPayload
            );
        }

        return VerifyResult::valid($status, $gatewayPaymentId, $sanitizedPayload);
    }

    public function verifyWebhook(Request $request, array $gatewayConfig): VerifyResult
    {
        $payload = $this->unwrapPayload($request->all());
        $sanitizedPayload = PayloadSanitizer::sanitize($payload);

        if (! $this->verifySignature($request, $gatewayConfig, true)) {
            return VerifyResult::invalid('Invalid Flitt webhook signature.', $sanitizedPayload);
        }

        $gatewayPaymentId = $this->extractGatewayPaymentId($payload)
            ?? (string) ($request->input('order_id') ?? '');
        $gatewayPaymentId = $gatewayPaymentId !== '' ? $gatewayPaymentId : null;

        $status = $this->extractStatus($payload);
        if ($status === Payment::STATUS_PENDING) {
            return VerifyResult::invalid(
                'Unable to determine Flitt payment status from webhook payload.',
                $sanitizedPayload
            );
        }

        return VerifyResult::valid($status, $gatewayPaymentId, $sanitizedPayload);
    }

    public function fetchPaymentStatus(string $gatewayPaymentId, array $gatewayConfig): StatusResult
    {
        $configuredStatusEndpoint = (string) data_get($gatewayConfig, 'extra.status_endpoint', '');

        if ($configuredStatusEndpoint === '') {
            return StatusResult::failure('Flitt status endpoint is not configured.');
        }

        return parent::fetchPaymentStatus($gatewayPaymentId, $gatewayConfig);
    }

    protected function buildCreatePayload(Payment $payment, array $gatewayConfig): array
    {
        $order = $payment->order()->firstOrFail();

        $merchantId = trim((string) ($gatewayConfig['client_id'] ?? ''));
        $password = trim((string) ($gatewayConfig['client_secret'] ?? ''));

        if ($merchantId === '' || $password === '') {
            throw new RuntimeException('Flitt credentials are missing (client/secret).');
        }

        Signature::merchant($merchantId);
        Signature::password($password);

        $serverCallbackUrl = trim((string) (
            $gatewayConfig['callback_url']
            ?? data_get($gatewayConfig, 'extra.server_callback_url')
            ?? data_get($gatewayConfig, 'extra.callback_url')
            ?? ''
        ));

        if ($serverCallbackUrl === '') {
            throw new RuntimeException('Flitt server_callback_url is missing.');
        }

        $requestPayload = [
            'server_callback_url' => $serverCallbackUrl,
            'order_id' => (string) data_get(
                $gatewayConfig,
                'extra.order_id',
                'Order ' . $order->code
            ),
            'order_desc' => (string) data_get(
                $gatewayConfig,
                'extra.order_desc',
                'Order #' . $order->code
            ),
            'currency' => (string) data_get($gatewayConfig, 'extra.currency', $payment->currency),
            'amount' => max(1, (int) round(((float) $payment->amount) * 100)),
            'merchant_id' => $merchantId,
        ];

        $rectoken = trim((string) data_get($gatewayConfig, 'extra.retoken', ''));
        if ($rectoken !== '') {
            $requestPayload['rectoken'] = $rectoken;
        }

        $payloadOverrides = data_get($gatewayConfig, 'extra.payload_overrides', []);
        if (is_array($payloadOverrides) && $payloadOverrides !== []) {
            $requestPayload = $this->mergePayloadRecursively($requestPayload, $payloadOverrides);
        }

        return [
            'request' => Arr::whereNotNull(Signature::sign($requestPayload)),
        ];
    }

    protected function verifySignature(Request $request, array $gatewayConfig, bool $webhook): bool
    {
        $merchantId = trim((string) ($gatewayConfig['client_id'] ?? ''));
        $password = trim((string) ($gatewayConfig['client_secret'] ?? ''));

        if ($merchantId === '' || $password === '') {
            return false;
        }

        Signature::merchant($merchantId);
        Signature::password($password);

        $payload = $this->unwrapPayload($webhook ? $request->all() : $request->query());
        if ($payload === []) {
            return false;
        }

        return Signature::check($payload);
    }

    protected function extractRedirectUrl(array $payload): ?string
    {
        $payload = $this->unwrapPayload($payload);

        $candidates = [
            data_get($payload, 'checkout_url'),
            data_get($payload, 'payment_url'),
            data_get($payload, 'redirect_url'),
            data_get($payload, 'url'),
            data_get($payload, 'response.checkout_url'),
            data_get($payload, 'response.payment_url'),
            data_get($payload, 'response.redirect_url'),
            data_get($payload, 'response.url'),
        ];

        foreach ($candidates as $candidate) {
            if (is_string($candidate) && trim($candidate) !== '') {
                return trim($candidate);
            }
        }

        return parent::extractRedirectUrl($payload);
    }

    protected function extractGatewayPaymentId(array $payload): ?string
    {
        $payload = $this->unwrapPayload($payload);

        $candidates = [
            data_get($payload, 'order_id'),
            data_get($payload, 'payment_id'),
            data_get($payload, 'recurring_id'),
            data_get($payload, 'transaction_id'),
            data_get($payload, 'response.order_id'),
            data_get($payload, 'response.payment_id'),
            data_get($payload, 'response.recurring_id'),
            data_get($payload, 'response.transaction_id'),
        ];

        foreach ($candidates as $candidate) {
            if ($candidate === null) {
                continue;
            }

            $value = trim((string) $candidate);
            if ($value !== '') {
                return $value;
            }
        }

        return parent::extractGatewayPaymentId($payload);
    }

    protected function extractStatus(array $payload): string
    {
        $payload = $this->unwrapPayload($payload);

        $statusCandidates = [
            data_get($payload, 'order_status'),
            data_get($payload, 'payment_status'),
            data_get($payload, 'status'),
            data_get($payload, 'response.order_status'),
            data_get($payload, 'response.payment_status'),
            data_get($payload, 'response.status'),
        ];

        foreach ($statusCandidates as $candidate) {
            if (! is_string($candidate)) {
                continue;
            }

            $value = strtolower(trim($candidate));

            if (in_array($value, ['approved', 'paid', 'success', 'successful', 'completed'], true)) {
                return Payment::STATUS_PAID;
            }

            if (in_array($value, ['declined', 'failed', 'rejected', 'error', 'canceled', 'cancelled'], true)) {
                return Payment::STATUS_FAILED;
            }
        }

        return parent::extractStatus($payload);
    }

    private function unwrapPayload(array $payload): array
    {
        $response = data_get($payload, 'response');

        if (is_array($response)) {
            return $response;
        }

        if (is_string($response) && trim($response) !== '') {
            $decoded = json_decode($response, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $decoded;
            }
        }

        return $payload;
    }

    private function mergePayloadRecursively(array $base, array $overrides): array
    {
        foreach ($overrides as $key => $value) {
            if (is_array($value) && isset($base[$key]) && is_array($base[$key])) {
                $base[$key] = $this->mergePayloadRecursively($base[$key], $value);
                continue;
            }

            $base[$key] = $value;
        }

        return $base;
    }
}
