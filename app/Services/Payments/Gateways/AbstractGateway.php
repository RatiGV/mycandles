<?php

namespace App\Services\Payments\Gateways;

use App\Contracts\PaymentGatewayInterface;
use App\Models\Payment;
use App\Services\Payments\Builders\InstallmentPayloadBuilder;
use App\Services\Payments\DTO\CreatePaymentResult;
use App\Services\Payments\DTO\StatusResult;
use App\Services\Payments\DTO\VerifyResult;
use App\Services\Payments\Support\PayloadSanitizer;
use App\Services\Payments\Support\PaymentStatusMapper;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Throwable;

abstract class AbstractGateway implements PaymentGatewayInterface
{
    public function __construct(private readonly InstallmentPayloadBuilder $installmentPayloadBuilder)
    {
    }

    abstract protected function providerName(): string;

    abstract protected function configBaseUrlKey(): string;

    protected function defaultCreateEndpoint(): string
    {
        return '/payments/create';
    }

    protected function defaultStatusEndpoint(): string
    {
        return '/payments/status/{id}';
    }

    public function createPayment(Payment $payment, array $gatewayConfig): CreatePaymentResult
    {
        $requestPayload = $this->buildCreatePayload($payment, $gatewayConfig);
        $sanitizedRequest = PayloadSanitizer::sanitize($requestPayload);

        try {
            $url = $this->buildUrl(
                $gatewayConfig,
                (string) data_get($gatewayConfig, 'extra.create_endpoint', $this->defaultCreateEndpoint())
            );
            if (! $url) {
                return CreatePaymentResult::failure('Gateway create endpoint is not configured.', [
                    'request' => $sanitizedRequest,
                ]);
            }

            // dd($this->buildRequestOptions($gatewayConfig, $requestPayload, true));

            $response = $this->requestWithRetry(
                gatewayConfig: $gatewayConfig,
                method: 'POST',
                url: $url,
                options: $this->buildRequestOptions($gatewayConfig, $requestPayload, true)
            );

            if ($response['error'] !== null) {
                return CreatePaymentResult::failure((string) $response['error'], [
                    'request' => $sanitizedRequest,
                ]);
            }

            $responseBody = $this->parseResponseBody((string) $response['body']);
            $sanitizedResponse = PayloadSanitizer::sanitize($responseBody);
            $httpStatus = (int) $response['status'];

            if (! ($httpStatus >= 200 && $httpStatus < 300)) {
                $errorMessage = $this->extractApiErrorMessage(
                    $responseBody,
                    'Gateway payment create request failed.'
                );

                return CreatePaymentResult::failure(
                    $errorMessage,
                    [
                        'http_status' => $httpStatus,
                        'request' => $sanitizedRequest,
                        'response' => $sanitizedResponse,
                    ]
                );
            }

            $redirectUrl = $this->extractRedirectUrl($responseBody);
            $gatewayPaymentId = $this->extractGatewayPaymentId($responseBody);

            if (! $redirectUrl) {
                return CreatePaymentResult::failure(
                    'Gateway did not return redirect URL.',
                    [
                        'request' => $sanitizedRequest,
                        'response' => $sanitizedResponse,
                    ]
                );
            }

            return CreatePaymentResult::success(
                redirectUrl: $redirectUrl,
                gatewayPaymentId: $gatewayPaymentId,
                raw: [
                    'request' => $sanitizedRequest,
                    'response' => $sanitizedResponse,
                ]
            );
        } catch (Throwable $e) {
            return CreatePaymentResult::failure(
                $e->getMessage(),
                ['request' => $sanitizedRequest]
            );
        }
    }

    public function verifyReturn(Request $request, array $gatewayConfig): VerifyResult
    {
        $payload = $request->query();
        $sanitizedPayload = PayloadSanitizer::sanitize($payload);

        if (! $this->verifySignature($request, $gatewayConfig, false)) {
            return VerifyResult::invalid('Invalid return signature.', $sanitizedPayload);
        }

        $gatewayPaymentId = $this->extractGatewayPaymentId($payload)
            ?? (string) ($request->query('gateway_payment_id') ?? '');
        $gatewayPaymentId = $gatewayPaymentId !== '' ? $gatewayPaymentId : null;

        $status = $this->extractStatus($payload);

        if ($status === Payment::STATUS_PENDING && $gatewayPaymentId) {
            $statusResult = $this->fetchPaymentStatus($gatewayPaymentId, $gatewayConfig);

            if ($statusResult->success) {
                return VerifyResult::valid($statusResult->status, $gatewayPaymentId, [
                    'return' => $sanitizedPayload,
                    'status_check' => PayloadSanitizer::sanitize($statusResult->raw),
                ]);
            }
        }

        if ($status === Payment::STATUS_PENDING) {
            return VerifyResult::invalid(
                'Unable to verify payment status from return payload.',
                $sanitizedPayload
            );
        }

        return VerifyResult::valid($status, $gatewayPaymentId, $sanitizedPayload);
    }

    public function verifyWebhook(Request $request, array $gatewayConfig): VerifyResult
    {
        $payload = $request->all();
        $sanitizedPayload = PayloadSanitizer::sanitize($payload);

        if (! $this->verifySignature($request, $gatewayConfig, true)) {
            return VerifyResult::invalid('Invalid webhook signature.', $sanitizedPayload);
        }

        $gatewayPaymentId = $this->extractGatewayPaymentId($payload)
            ?? (string) ($request->input('gateway_payment_id') ?? '');
        $gatewayPaymentId = $gatewayPaymentId !== '' ? $gatewayPaymentId : null;

        $status = $this->extractStatus($payload);

        if ($status === Payment::STATUS_PENDING && $gatewayPaymentId) {
            $statusResult = $this->fetchPaymentStatus($gatewayPaymentId, $gatewayConfig);

            if ($statusResult->success) {
                return VerifyResult::valid($statusResult->status, $gatewayPaymentId, [
                    'webhook' => $sanitizedPayload,
                    'status_check' => PayloadSanitizer::sanitize($statusResult->raw),
                ]);
            }
        }

        if ($status === Payment::STATUS_PENDING) {
            return VerifyResult::invalid(
                'Unable to verify payment status from webhook payload.',
                $sanitizedPayload
            );
        }

        return VerifyResult::valid($status, $gatewayPaymentId, $sanitizedPayload);
    }

    public function fetchPaymentStatus(string $gatewayPaymentId, array $gatewayConfig): StatusResult
    {
        try {
            $statusEndpoint = (string) data_get($gatewayConfig, 'extra.status_endpoint', $this->defaultStatusEndpoint());
            $statusEndpoint = str_replace('{id}', $gatewayPaymentId, $statusEndpoint);
            $url = $this->buildUrl($gatewayConfig, $statusEndpoint);

            if (! $url) {
                return StatusResult::failure('Gateway status endpoint is not configured.');
            }

            $response = $this->requestWithRetry(
                gatewayConfig: $gatewayConfig,
                method: 'GET',
                url: $url,
                options: $this->buildRequestOptions($gatewayConfig, [], false)
            );

            if ($response['error'] !== null) {
                return StatusResult::failure((string) $response['error']);
            }

            $responseBody = $this->parseResponseBody((string) $response['body']);
            $httpStatus = (int) $response['status'];

            if (! ($httpStatus >= 200 && $httpStatus < 300)) {
                return StatusResult::failure('Gateway payment status request failed.', [
                    'error' => $this->extractApiErrorMessage($responseBody, 'Gateway payment status request failed.'),
                    'http_status' => $httpStatus,
                    'response' => PayloadSanitizer::sanitize($responseBody),
                ]);
            }

            return StatusResult::success(
                $this->extractStatus($responseBody),
                PayloadSanitizer::sanitize($responseBody)
            );
        } catch (Throwable $e) {
            return StatusResult::failure($e->getMessage());
        }
    }

    protected function buildCreatePayload(Payment $payment, array $gatewayConfig): array
    {
        $order = $payment->order()->with('products', 'user')->firstOrFail();

        $payload = [
            'amount' => (float) $payment->amount,
            'currency' => $payment->currency,
            'order_id' => (string) $order->code,
            'description' => sprintf('Order #%s', $order->code),
            'payment_id' => $payment->id,
            'method' => $payment->method,
            'return_url' => $gatewayConfig['return_url'] ?? data_get($gatewayConfig, 'extra.return_url'),
            'callback_url' => $gatewayConfig['callback_url'] ?? data_get($gatewayConfig, 'extra.callback_url'),
            'client_id' => $gatewayConfig['client_id'] ?? null,
            'metadata' => [
                'payment_id' => $payment->id,
                'order_id' => $order->id,
                'provider' => $payment->provider,
            ],
        ];

        if ($payment->method === Payment::METHOD_INSTALLMENT) {
            $payload['installment'] = $this->installmentPayloadBuilder->build($order);
        }

        return Arr::whereNotNull($payload);
    }

    protected function httpClient(array $gatewayConfig): Client
    {
        return new Client([
            'timeout' => (float) data_get($gatewayConfig, 'extra.timeout', 15),
            'connect_timeout' => (float) data_get($gatewayConfig, 'extra.connect_timeout', 10),
            'verify' => (bool) data_get($gatewayConfig, 'extra.verify_ssl', config('services.payments.ssl_verify', true)),
            'http_errors' => false,
        ]);
    }

    protected function buildRequestOptions(array $gatewayConfig, array $payload = [], bool $asJson = true): array
    {
        $options = [
            'headers' => [
                'Accept' => 'application/json',
            ],
        ];

        if ($asJson) {
            $options['json'] = $payload;
            $options['headers']['Content-Type'] = 'application/json';
        }

        $authType = (string) data_get($gatewayConfig, 'extra.auth_type', 'basic');
        $clientId = (string) ($gatewayConfig['client_id'] ?? '');
        $clientSecret = (string) ($gatewayConfig['client_secret'] ?? '');

        if ($authType === 'bearer') {
            $token = (string) data_get($gatewayConfig, 'extra.access_token', '');
            if ($token !== '') {
                $options['headers']['Authorization'] = 'Bearer ' . $token;
            }
        } elseif ($authType === 'header') {
            $headerName = (string) data_get($gatewayConfig, 'extra.auth_header_name', 'X-Api-Key');
            $headerValue = (string) data_get($gatewayConfig, 'extra.auth_header_value', $clientSecret);
            if ($headerName !== '' && $headerValue !== '') {
                $options['headers'][$headerName] = $headerValue;
            }
        } elseif ($authType === 'basic' && ($clientId !== '' || $clientSecret !== '')) {
            $options['auth'] = [$clientId, $clientSecret];
        }

        $extraHeaders = data_get($gatewayConfig, 'extra.headers', []);
        if (is_array($extraHeaders) && $extraHeaders !== []) {
            $options['headers'] = array_merge($options['headers'], $extraHeaders);
        }

        return $options;
    }

    protected function requestWithRetry(array $gatewayConfig, string $method, string $url, array $options): array
    {
        $client = $this->httpClient($gatewayConfig);
        $maxRetries = max(0, (int) data_get($gatewayConfig, 'extra.retry_attempts', 2));
        $delayMs = max(0, (int) data_get($gatewayConfig, 'extra.retry_delay_ms', 300));

        $attempt = 0;
        $lastError = null;

        while (true) {
            try {
                $response = $client->request($method, $url, $options);

                return [
                    'status' => $response->getStatusCode(),
                    'body' => (string) $response->getBody(),
                    'error' => null,
                ];
            } catch (GuzzleException $e) {
                $lastError = $e->getMessage();
            }

            if ($attempt >= $maxRetries) {
                break;
            }

            $attempt++;

            if ($delayMs > 0) {
                usleep($delayMs * 1000);
            }
        }

        return [
            'status' => 0,
            'body' => '',
            'error' => $lastError ?? 'Gateway request failed.',
        ];
    }

    protected function verifySignature(Request $request, array $gatewayConfig, bool $webhook): bool
    {
        $signatureSecret = (string) data_get($gatewayConfig, 'extra.signature_secret', '');

        if ($signatureSecret === '') {
            return true;
        }

        $signatureField = (string) data_get(
            $gatewayConfig,
            $webhook ? 'extra.webhook_signature_param' : 'extra.return_signature_param',
            'signature'
        );
        $signatureHeader = (string) data_get(
            $gatewayConfig,
            $webhook ? 'extra.webhook_signature_header' : 'extra.return_signature_header',
            ''
        );

        $providedSignature = null;

        if ($signatureHeader !== '') {
            $providedSignature = $request->header($signatureHeader);
        }

        if (! $providedSignature && $signatureField !== '') {
            $providedSignature = $request->input($signatureField, $request->query($signatureField));
        }

        if (! $providedSignature) {
            return false;
        }

        $payload = $webhook ? $request->all() : $request->query();

        if ($signatureField !== '') {
            unset($payload[$signatureField]);
        }

        ksort($payload);
        $message = http_build_query($payload);
        $expectedSignature = hash_hmac('sha256', $message, $signatureSecret);

        return hash_equals($expectedSignature, (string) $providedSignature);
    }

    protected function buildUrl(array $gatewayConfig, string $endpoint): ?string
    {

        $endpoint = trim($endpoint);
        if ($endpoint === '') {
            return null;
        }

        if (preg_match('/^https?:\/\//i', $endpoint) === 1) {
            return $endpoint;
        }

        $baseUrl = (string) data_get($gatewayConfig, 'extra.base_url', config($this->configBaseUrlKey()));
        $baseUrl = rtrim($baseUrl, '/');
        if ($baseUrl === '') {
            return null;
        }

        $path = '/' . ltrim($endpoint, '/');
        return $baseUrl . $path;
    }

    protected function extractRedirectUrl(array $payload): ?string
    {
        $candidates = [
            data_get($payload, 'redirect_url'),
            data_get($payload, 'payment_url'),
            data_get($payload, 'links.redirect'),
            data_get($payload, 'links.payment'),
            data_get($payload, 'data.redirect_url'),
            data_get($payload, 'data.payment_url'),
            data_get($payload, 'data.links.redirect'),
        ];

        foreach ($candidates as $candidate) {
            if (is_string($candidate) && $candidate !== '') {
                return $candidate;
            }
        }

        return null;
    }

    protected function extractGatewayPaymentId(array $payload): ?string
    {
        $candidates = [
            data_get($payload, 'id'),
            data_get($payload, 'payment_id'),
            data_get($payload, 'order_id'),
            data_get($payload, 'data.id'),
            data_get($payload, 'data.payment_id'),
        ];

        foreach ($candidates as $candidate) {
            if ($candidate === null) {
                continue;
            }

            $value = (string) $candidate;

            if ($value !== '') {
                return $value;
            }
        }

        return null;
    }

    protected function extractStatus(array $payload): string
    {
        $statusCandidates = [
            data_get($payload, 'status'),
            data_get($payload, 'payment_status'),
            data_get($payload, 'result.status'),
            data_get($payload, 'data.status'),
            data_get($payload, 'data.payment_status'),
            data_get($payload, 'success'),
        ];

        foreach ($statusCandidates as $candidate) {
            if (is_bool($candidate)) {
                return $candidate ? Payment::STATUS_PAID : Payment::STATUS_FAILED;
            }

            if ($candidate !== null && (string) $candidate !== '') {
                return PaymentStatusMapper::normalize((string) $candidate);
            }
        }

        return Payment::STATUS_PENDING;
    }

    protected function parseResponseBody(string $body): array
    {
        if ($body === '') {
            return [];
        }

        $decoded = json_decode($body, true);

        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return $decoded;
        }

        return ['raw_body' => $body];
    }

    protected function extractApiErrorMessage(array $payload, string $fallback): string
    {
        $candidates = [
            data_get($payload, 'message'),
            data_get($payload, 'detail'),
            data_get($payload, 'error.message'),
            data_get($payload, 'errors.0.message'),
            data_get($payload, 'errors.0.detail'),
        ];

        foreach ($candidates as $candidate) {
            if (is_string($candidate) && trim($candidate) !== '') {
                return trim($candidate);
            }
        }

        return $fallback;
    }
}
