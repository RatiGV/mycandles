<?php

namespace App\Services\Payments\Gateways;

use App\Models\PaymentGateaway;
use App\Models\Payment;
use App\Models\Product;
use App\Services\Payments\Support\PayloadSanitizer;
use Illuminate\Support\Arr;
use RuntimeException;

class TbcGateway extends FlittGateway
{
    private const DEFAULT_OAUTH_TOKEN_URL = 'https://api.tbcbank.ge/oauth/token';
    private const DEFAULT_INSTALLMENT_REDIRECT_BASE_URL = 'https://tbcganvadeba.ge/auth';

    protected function providerName(): string
    {
        return Payment::PROVIDER_TBC;
    }

    protected function configBaseUrlKey(): string
    {
        return 'services.payments.tbc_base_url';
    }

    protected function defaultCreateEndpoint(): string
    {
        return '/api/checkout/url';
    }

    protected function defaultStatusEndpoint(): string
    {
        return '/api/recurring/status/{id}';
    }

    protected function buildCreatePayload(Payment $payment, array $gatewayConfig): array
    {
        if (! $this->isInstallmentGateway($gatewayConfig)) {
            return parent::buildCreatePayload($payment, $gatewayConfig);
        }

        $order = $payment->order()->with('products')->firstOrFail();
        $merchantKey = $this->resolveInstallmentMerchantKey($gatewayConfig);
        $campaignId = data_get($gatewayConfig, 'extra.campaign_id');

        $productIds = $order->products
            ->pluck('product_id')
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->values()
            ->all();

        $productsById = Product::query()
            ->with('trans')
            ->whereIn('id', $productIds)
            ->get()
            ->keyBy('id');

        $products = $order->products->map(function ($item) use ($productsById) {
            $productId = (int) ($item->product_id ?? 0);
            $product = $productsById->get($productId);
            $productName = trim((string) (
                data_get($product, 'trans.title')
                ?? ($productId > 0 ? 'Product #' . $productId : 'Product')
            ));

            return [
                'name' => $productName !== '' ? $productName : 'Product',
                'price' => round((float) $item->unit_price, 2),
                'quantity' => max(1, (int) $item->qty),
            ];
        })->values()->all();

        if ($products === []) {
            $products[] = [
                'name' => 'Order #' . $order->code,
                'price' => round((float) $payment->amount, 2),
                'quantity' => 1,
            ];
        }

        $payload = [
            'merchantKey' => $merchantKey,
            'priceTotal' => round((float) $payment->amount, 2),
            'invoiceId' => (string) data_get($gatewayConfig, 'extra.invoice_id', $order->code),
            'products' => $products,
        ];

        if ($campaignId !== null && (string) $campaignId !== '') {
            $payload['campaignId'] = (string) $campaignId;
        }

        $payloadOverrides = data_get($gatewayConfig, 'extra.payload_overrides', []);
        if (is_array($payloadOverrides) && $payloadOverrides !== []) {
            $payload = $this->mergePayloadRecursively($payload, $payloadOverrides);
        }

        return Arr::whereNotNull($payload);
    }

    protected function buildRequestOptions(array $gatewayConfig, array $payload = [], bool $asJson = true): array
    {
        $options = parent::buildRequestOptions($gatewayConfig, $payload, $asJson);

        if (! $this->isInstallmentGateway($gatewayConfig)) {
            return $options;
        }

        $accessToken = $this->resolveInstallmentAccessToken($gatewayConfig);
        unset($options['auth']);

        $options['headers']['Authorization'] = 'Bearer ' . $accessToken;

        return $options;
    }

    protected function extractRedirectUrl(array $payload): ?string
    {
        $sessionId = trim((string) data_get($payload, 'sessionId', data_get($payload, 'data.sessionId', '')));

        if ($sessionId !== '') {
            $baseRedirectUrl = trim((string) config(
                'services.payments.tbc_installment_redirect_base_url',
                self::DEFAULT_INSTALLMENT_REDIRECT_BASE_URL
            ));
            $separator = str_contains($baseRedirectUrl, '?') ? '&' : '?';

            return $baseRedirectUrl . $separator . 'sessionId=' . rawurlencode($sessionId);
        }

        return parent::extractRedirectUrl($payload);
    }

    protected function extractGatewayPaymentId(array $payload): ?string
    {
        $sessionId = trim((string) data_get($payload, 'sessionId', data_get($payload, 'data.sessionId', '')));

        if ($sessionId !== '') {
            return $sessionId;
        }

        return parent::extractGatewayPaymentId($payload);
    }

    private function resolveInstallmentAccessToken(array $gatewayConfig): string
    {
        [$clientId, $clientSecret] = $this->resolveCredentialPair($gatewayConfig);

        if ($clientId === '' || $clientSecret === '') {
            throw new RuntimeException('TBC installment credentials are missing (client_id/client_secret).');
        }

        $tokenUrl = (string) data_get(
            $gatewayConfig,
            'extra.token_url',
            config('services.payments.tbc_oauth_token_url', self::DEFAULT_OAUTH_TOKEN_URL)
        );

        if ($tokenUrl === '') {
            throw new RuntimeException('TBC OAuth token URL is not configured.');
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
            throw new RuntimeException('Failed to fetch TBC OAuth token: ' . $response['error']);
        }

        $body = $this->parseResponseBody((string) $response['body']);
        $httpStatus = (int) $response['status'];

        if (! ($httpStatus >= 200 && $httpStatus < 300)) {
            throw new RuntimeException(
                'Failed to fetch TBC OAuth token: ' . json_encode(PayloadSanitizer::sanitize($body))
            );
        }

        $accessToken = trim((string) data_get($body, 'access_token', ''));
        if ($accessToken === '') {
            throw new RuntimeException('TBC OAuth response does not contain access_token.');
        }

        return $accessToken;
    }

    private function resolveCredentialPair(array $gatewayConfig): array
    {
        $clientId = trim((string) ($gatewayConfig['client_id'] ?? ''));
        $clientSecret = trim((string) ($gatewayConfig['client_secret'] ?? ''));

        if (! $this->isInstallmentGateway($gatewayConfig)) {
            return [$clientId, $clientSecret];
        }

        $installmentGateway = PaymentGateaway::query()
            ->where('name', PaymentGateaway::NAME_TBC_INSTALLMENT)
            ->first();

        if (! $installmentGateway) {
            return [$clientId, $clientSecret];
        }

        $dbClient = trim((string) ($installmentGateway->client ?? $installmentGateway->client_id ?? ''));
        $dbSecret = trim((string) ($installmentGateway->secret ?? $installmentGateway->client_secret ?? ''));

        if ($dbClient === '' || $dbSecret === '') {
            return [$clientId, $clientSecret];
        }

        return [$dbClient, $dbSecret];
    }

    private function resolveInstallmentMerchantKey(array $gatewayConfig): string
    {
        $merchantKey = trim((string) (
            $gatewayConfig['merchant_key']
            ?? data_get($gatewayConfig, 'extra.merchant_key')
            ?? ''
        ));

        if ($merchantKey !== '') {
            return $merchantKey;
        }

        $installmentGateway = PaymentGateaway::query()
            ->where('name', PaymentGateaway::NAME_TBC_INSTALLMENT)
            ->first();

        $dbMerchantKey = trim((string) (
            $installmentGateway->merchant_key
            ?? data_get($installmentGateway, 'extra.merchant_key')
            ?? ''
        ));

        if ($dbMerchantKey === '') {
            throw new RuntimeException('TBC installment merchant_key is missing.');
        }

        return $dbMerchantKey;
    }

    private function isInstallmentGateway(array $gatewayConfig): bool
    {
        return (string) ($gatewayConfig['name'] ?? '') === PaymentGateaway::NAME_TBC_INSTALLMENT;
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
