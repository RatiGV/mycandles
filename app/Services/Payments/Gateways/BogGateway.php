<?php

namespace App\Services\Payments\Gateways;

use App\Models\PaymentGateaway;
use App\Models\Payment;
use App\Services\Payments\DTO\CreatePaymentResult;
use App\Services\Payments\DTO\StatusResult;
use App\Services\Payments\DTO\VerifyResult;
use App\Services\Payments\Support\PayloadSanitizer;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use RuntimeException;
use Throwable;

class BogGateway extends AbstractGateway
{
    private const DEFAULT_TOKEN_URL = 'https://oauth2.bog.ge/auth/realms/bog/protocol/openid-connect/token';

    protected function providerName(): string
    {
        return Payment::PROVIDER_BOG;
    }

    protected function configBaseUrlKey(): string
    {
        return 'services.payments.bog_base_url';
    }

    protected function defaultCreateEndpoint(): string
    {
        return '/payments/v1/ecommerce/orders';
    }

    protected function defaultStatusEndpoint(): string
    {
        return '/payments/v1/receipt/{id}';
    }

    public function createPayment(Payment $payment, array $gatewayConfig): CreatePaymentResult
    {
        return parent::createPayment($payment, $gatewayConfig);
    }

    public function fetchPaymentStatus(string $gatewayPaymentId, array $gatewayConfig): StatusResult
    {
        $gatewayPaymentId = trim($gatewayPaymentId);

        if ($gatewayPaymentId === '') {
            return StatusResult::failure('BOG gateway payment id is empty.');
        }

        try {
            $statusUrls = $this->buildStatusUrls($gatewayPaymentId, $gatewayConfig);
            $requestOptions = $this->buildRequestOptions($gatewayConfig, [], false);
            $attempts = [];

            foreach ($statusUrls as $url) {
                $response = $this->requestWithRetry(
                    gatewayConfig: $gatewayConfig,
                    method: 'GET',
                    url: $url,
                    options: $requestOptions
                );

                if ($response['error'] !== null) {
                    $attempts[] = [
                        'url' => $url,
                        'error' => $response['error'],
                    ];
                    continue;
                }

                $responseBody = $this->parseResponseBody((string) $response['body']);
                $httpStatus = (int) $response['status'];

                $attempts[] = [
                    'url' => $url,
                    'http_status' => $httpStatus,
                    'response' => PayloadSanitizer::sanitize($responseBody),
                ];

                if ($httpStatus >= 200 && $httpStatus < 300) {
                    return StatusResult::success(
                        $this->extractStatus($responseBody),
                        [
                            'url' => $url,
                            'response' => PayloadSanitizer::sanitize($responseBody),
                        ]
                    );
                }
            }

            return StatusResult::failure('BOG payment status request failed for all endpoints.', [
                'attempts' => $attempts,
            ]);
        } catch (Throwable $e) {
            return StatusResult::failure($e->getMessage());
        }
    }

    public function verifyReturn(Request $request, array $gatewayConfig): VerifyResult
    {
        $sanitizedReturnPayload = PayloadSanitizer::sanitize($request->query());

        $gatewayPaymentId = (string) ($request->query('gateway_payment_id') ?? $request->query('order_id') ?? '');
        if ($gatewayPaymentId === '') {
            return VerifyResult::invalid(
                'Missing gateway payment id for BOG return verification.',
                $sanitizedReturnPayload
            );
        }

        $statusResult = $this->fetchPaymentStatus($gatewayPaymentId, $gatewayConfig);

        if (! $statusResult->success) {
            return VerifyResult::invalid(
                'Unable to verify BOG payment status on return.',
                [
                    'return' => $sanitizedReturnPayload,
                    'status_check' => PayloadSanitizer::sanitize($statusResult->raw),
                ]
            );
        }

        return VerifyResult::valid(
            $statusResult->status,
            $gatewayPaymentId,
            [
                'return' => $sanitizedReturnPayload,
                'status_check' => PayloadSanitizer::sanitize($statusResult->raw),
            ]
        );
    }

    protected function buildCreatePayload(Payment $payment, array $gatewayConfig): array
    {
        $order = $payment->order()->with('products')->firstOrFail();

        $basket = $order->products->map(function ($item) {
            $unitPrice = (float) $item->unit_price;
            $qty = max(1, (int) $item->qty);

            return [
                'product_id' => (string) $item->product_id,
                'description' => 'Product #' . $item->product_id,
                'quantity' => $qty,
                'unit_price' => round($unitPrice, 2),
                'total_price' => round($unitPrice * $qty, 2),
            ];
        })->values()->all();

        $baseReturnUrl = $this->ensureHttpsUrl((string) ($gatewayConfig['return_url'] ?? data_get($gatewayConfig, 'extra.return_url', '')));
        $successUrl = $this->appendQueryString((string) $baseReturnUrl, [
            'payment_id' => $payment->id,
            'status' => 'success',
        ]);
        $failUrl = $this->appendQueryString((string) $baseReturnUrl, [
            'payment_id' => $payment->id,
            'status' => 'fail',
        ]);
        $callbackUrl = $this->ensureHttpsUrl((string) ($gatewayConfig['callback_url'] ?? data_get($gatewayConfig, 'extra.callback_url', '')));

        $payload = [
            'callback_url' => $callbackUrl,
            'purchase_units' => [
                'currency' => $payment->currency,
                'total_amount' => round((float) $payment->amount, 2),
                'basket' => $basket,
            ],
            'redirect_urls' => [
                'success' => $successUrl,
                'fail' => $failUrl,
            ]
        ];

        if ((bool) data_get($gatewayConfig, 'extra.include_external_order_id', false)) {
            $payload['external_order_id'] = (string) $payment->id;
        }

        if ($payment->method === Payment::METHOD_INSTALLMENT) {
            $payload['purchase_units']['transfer_method'] = 'installment';
        }

        $ttl = data_get($gatewayConfig, 'extra.ttl');
        if ($ttl !== null && is_numeric($ttl)) {
            $payload['ttl'] = (int) $ttl;
        }

        $paymentMethod = data_get($gatewayConfig, 'extra.payment_method');
        if (is_string($paymentMethod) && $paymentMethod !== '') {
            $payload['payment_method'] = [$paymentMethod];
        } elseif (is_array($paymentMethod) && $paymentMethod !== []) {
            $payload['payment_method'] = array_values($paymentMethod);
        }

        $payloadOverrides = data_get($gatewayConfig, 'extra.payload_overrides', []);
        if (is_array($payloadOverrides) && $payloadOverrides !== []) {
            $payload = $this->mergePayloadRecursively($payload, $payloadOverrides);
        }

        return Arr::whereNotNull($payload);
    }

    protected function buildRequestOptions(array $gatewayConfig, array $payload = [], bool $asJson = true): array
    {
        $accessToken = $this->resolveAccessToken($gatewayConfig);

        $options = parent::buildRequestOptions($gatewayConfig, $payload, $asJson);
        unset($options['auth']);

        $options['headers']['Authorization'] = 'Bearer ' . $accessToken;

        if (! array_key_exists('Accept-Language', $options['headers'])) {
            $options['headers']['Accept-Language'] = (string) data_get(
                $gatewayConfig,
                'extra.accept_language',
                config('services.payments.bog_accept_language', 'en')
            );
        }

        return $options;
    }

    protected function verifySignature(Request $request, array $gatewayConfig, bool $webhook): bool
    {
        if (! $webhook) {
            return true;
        }

        $signature = (string) $request->header('Callback-Signature', '');
        if ($signature === '') {
            return false;
        }

        $decodedSignature = base64_decode($signature, true);
        if ($decodedSignature === false) {
            return false;
        }

        $publicKey = $this->resolveCallbackPublicKey($gatewayConfig);
        if (! $publicKey) {
            return false;
        }

        return openssl_verify(
            $request->getContent(),
            $decodedSignature,
            $publicKey,
            OPENSSL_ALGO_SHA256
        ) === 1;
    }

    protected function extractRedirectUrl(array $payload): ?string
    {
        $redirect = data_get($payload, '_links.redirect.href');

        if (is_string($redirect) && $redirect !== '') {
            return $redirect;
        }

        return parent::extractRedirectUrl($payload);
    }

    protected function extractGatewayPaymentId(array $payload): ?string
    {
        $orderId = data_get($payload, 'order_id');
        if (is_string($orderId) && $orderId !== '') {
            return $orderId;
        }

        $detailsUrl = data_get($payload, '_links.details.href');
        if (is_string($detailsUrl) && $detailsUrl !== '') {
            $path = parse_url($detailsUrl, PHP_URL_PATH) ?: '';
            $id = basename((string) $path);

            if ($id !== '' && $id !== '/') {
                return $id;
            }
        }

        return parent::extractGatewayPaymentId($payload);
    }

    protected function extractStatus(array $payload): string
    {
        $transactionStatus = strtolower((string) data_get($payload, 'transaction_status', data_get($payload, 'payment_detail.transaction_status', '')));

        if (in_array($transactionStatus, ['approved', 'success', 'completed'], true)) {
            return Payment::STATUS_PAID;
        }

        if (in_array($transactionStatus, ['rejected', 'failed', 'refunded', 'partially_refunded', 'chargeback', 'partially_chargeback'], true)) {
            return Payment::STATUS_FAILED;
        }

        $transferStatus = strtolower((string) data_get($payload, 'purchase_units.transfer_status', ''));

        if (in_array($transferStatus, ['success', 'succeeded', 'completed'], true)) {
            return Payment::STATUS_PAID;
        }

        if (in_array($transferStatus, ['failed', 'declined', 'rejected'], true)) {
            return Payment::STATUS_FAILED;
        }

        $orderStatus = strtolower((string) data_get($payload, 'order_status', ''));

        if ($orderStatus === 'completed' && $transactionStatus === '') {
            return Payment::STATUS_PAID;
        }

        if (in_array($orderStatus, ['created', 'processing'], true)) {
            return Payment::STATUS_PENDING;
        }

        return parent::extractStatus($payload);
    }

    private function resolveAccessToken(array $gatewayConfig): string
    {
        [$clientId, $clientSecret] = $this->resolveCredentialPair($gatewayConfig);

        if ($clientId === '' || $clientSecret === '') {
            throw new RuntimeException('BOG credentials are missing (client_id/client_secret).');
        }

        $tokenUrl = (string) data_get($gatewayConfig, 'extra.token_url', config('services.payments.bog_oauth_token_url', self::DEFAULT_TOKEN_URL));
        if ($tokenUrl === '') {
            throw new RuntimeException('BOG OAuth token URL is not configured.');
        }

        $response = $this->requestWithRetry(
            gatewayConfig: $gatewayConfig,
            method: 'POST',
            url: $tokenUrl,
            options: [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ],
                'auth' => [$clientId, $clientSecret],
                'form_params' => [
                    'grant_type' => 'client_credentials',
                ],
            ]
        );


        if ($response['error'] !== null) {
            throw new RuntimeException('Failed to fetch BOG OAuth token: ' . $response['error']);
        }


        $body = $this->parseResponseBody((string) $response['body']);

        $httpStatus = (int) $response['status'];

        if (! ($httpStatus >= 200 && $httpStatus < 300)) {
            throw new RuntimeException(
                'Failed to fetch BOG OAuth token: ' . json_encode(PayloadSanitizer::sanitize($body))
            );
        }

        $accessToken = (string) data_get($body, 'access_token', '');
        if ($accessToken === '') {
            throw new RuntimeException('BOG OAuth response does not contain access_token.');
        }

        return $accessToken;
    }

    private function resolveCredentialPair(array $gatewayConfig): array
    {
        $gatewayName = (string) ($gatewayConfig['name'] ?? '');
        $clientId = trim((string) ($gatewayConfig['client_id'] ?? ''));
        $clientSecret = trim((string) ($gatewayConfig['client_secret'] ?? ''));

        if ($gatewayName !== PaymentGateaway::NAME_BOG_INSTALLMENT) {
            return [$clientId, $clientSecret];
        }

        $installmentGateway = PaymentGateaway::query()
            ->where('name', PaymentGateaway::NAME_BOG_INSTALLMENT)
            ->first();

        if (! $installmentGateway) {
            return [$clientId, $clientSecret];
        }

        $dbClient = trim((string) (
            $installmentGateway->client
            ?? $installmentGateway->client_id
            ?? ''
        ));
        $dbSecret = trim((string) (
            $installmentGateway->secret
            ?? $installmentGateway->client_secret
            ?? ''
        ));

        if ($dbClient === '' || $dbSecret === '') {
            return [$clientId, $clientSecret];
        }

        return [$dbClient, $dbSecret];
    }

    private function resolveCallbackPublicKey(array $gatewayConfig): ?string
    {
        $configuredKey = (string) data_get($gatewayConfig, 'extra.callback_public_key', '');
        if ($configuredKey !== '') {
            return $this->normalizePublicKey($configuredKey);
        }

        $cacheKey = 'payments:bog:callback-key:' . sha1((string) ($gatewayConfig['client_id'] ?? ''));
        $cached = Cache::get($cacheKey);

        if (is_string($cached) && $cached !== '') {
            return $cached;
        }

        $publicKeyEndpoint = (string) data_get($gatewayConfig, 'extra.public_key_endpoint', '/payments/v1/receipt/public-key');
        $url = $this->buildUrl($gatewayConfig, $publicKeyEndpoint);

        if (! $url) {
            return null;
        }

        try {
            $response = $this->requestWithRetry(
                gatewayConfig: $gatewayConfig,
                method: 'GET',
                url: $url,
                options: $this->buildRequestOptions($gatewayConfig, [], false)
            );

            if ($response['error'] !== null) {
                return null;
            }

            $httpStatus = (int) $response['status'];
            if (! ($httpStatus >= 200 && $httpStatus < 300)) {
                return null;
            }

            $body = $this->parseResponseBody((string) $response['body']);
            $key = (string) data_get($body, 'key', '');
            if ($key === '') {
                return null;
            }

            $normalized = $this->normalizePublicKey($key);

            Cache::put($cacheKey, $normalized, now()->addDay());

            return $normalized;
        } catch (Throwable) {
            return null;
        }
    }

    private function normalizePublicKey(string $publicKey): string
    {
        $trimmed = trim($publicKey);

        if (str_contains($trimmed, 'BEGIN PUBLIC KEY')) {
            return $trimmed;
        }

        return "-----BEGIN PUBLIC KEY-----\n"
            . trim(chunk_split(str_replace(["\r", "\n"], '', $trimmed), 64, "\n"))
            . "\n-----END PUBLIC KEY-----";
    }

    private function appendQueryString(string $url, array $params): string
    {
        if ($url === '') {
            return '';
        }

        $separator = str_contains($url, '?') ? '&' : '?';

        return $url . $separator . http_build_query($params);
    }

    private function ensureHttpsUrl(string $url): string
    {
        $trimmed = trim($url);

        if ($trimmed === '') {
            return '';
        }

        if (str_starts_with($trimmed, 'http://')) {
            return 'https://' . substr($trimmed, 7);
        }

        return $trimmed;
    }

    private function buildStatusUrls(string $gatewayPaymentId, array $gatewayConfig): array
    {
        $urls = [];

        if (filter_var($gatewayPaymentId, FILTER_VALIDATE_URL)) {
            $urls[] = $gatewayPaymentId;
        }

        $configuredEndpoint = (string) data_get($gatewayConfig, 'extra.status_endpoint', '');
        if ($configuredEndpoint !== '') {
            if (str_contains($configuredEndpoint, '{id}')) {
                $endpoint = str_replace('{id}', rawurlencode($gatewayPaymentId), $configuredEndpoint);
            } else {
                $endpoint = rtrim($configuredEndpoint, '/') . '/' . rawurlencode($gatewayPaymentId);
            }

            $configuredUrl = $this->buildUrl($gatewayConfig, $endpoint);
            if ($configuredUrl) {
                $urls[] = $configuredUrl;
            }
        }

        $receiptUrl = $this->buildUrl($gatewayConfig, '/payments/v1/receipt/' . rawurlencode($gatewayPaymentId));
        if ($receiptUrl) {
            $urls[] = $receiptUrl;
        }

        $orderDetailsUrl = $this->buildUrl($gatewayConfig, '/payments/v1/ecommerce/orders/' . rawurlencode($gatewayPaymentId));
        if ($orderDetailsUrl) {
            $urls[] = $orderDetailsUrl;
        }

        return array_values(array_unique($urls));
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
