<?php

namespace App\Services\Payments;

use App\Models\PaymentGateaway;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;

class GatewayConfigResolver
{
    private ?array $schemaColumns = null;

    public function resolve(string $provider, string $method, bool $requireActive = true): array
    {
        $gatewayName = PaymentGateaway::nameFor($provider, $method);

        if (! $gatewayName) {
            throw ValidationException::withMessages([
                'payment_method' => ['Unsupported payment method.'],
            ]);
        }

        return $this->resolveByName($gatewayName, $requireActive);
    }

    public function resolveByName(string $gatewayName, bool $requireActive = true): array
    {
        $definitions = PaymentGateaway::checkoutDefinitions();

        if (! isset($definitions[$gatewayName])) {
            throw ValidationException::withMessages([
                'payment_method' => ['Unsupported payment method.'],
            ]);
        }

        $gateway = PaymentGateaway::query()->where('name', $gatewayName)->first();

        if (! $gateway) {
            throw ValidationException::withMessages([
                'payment_method' => ['Payment method is not configured in admin panel.'],
            ]);
        }

        if ($requireActive && ! (bool) $gateway->status) {
            throw ValidationException::withMessages([
                'payment_method' => ['Selected payment method is disabled.'],
            ]);
        }

        if ($requireActive && ! $this->hasGatewayCredentials($gateway)) {
            throw ValidationException::withMessages([
                'payment_method' => ['Selected payment method is not configured.'],
            ]);
        }

        $extra = $this->applyGatewayExtraDefaults(
            $gateway->name,
            $this->resolveExtra($gateway)
        );
        $definition = $definitions[$gatewayName];
        $clientId = $this->resolveClientId($gateway);
        $clientSecret = $this->resolveClientSecret($gateway);
        $merchantKey = $this->resolveMerchantKey($gateway, $extra);

        return [
            'name' => $gateway->name,
            'provider' => $definition['provider'],
            'method' => $definition['method'],
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'merchant_key' => $merchantKey,
            'sandbox' => $this->columnExists('sandbox') ? (bool) ($gateway->sandbox ?? false) : (bool) ($extra['sandbox'] ?? false),
            'extra' => $extra,
            'is_active' => (bool) $gateway->status,
        ];
    }

    public function activeCheckoutMethods(): array
    {
        $definitions = PaymentGateaway::checkoutDefinitions();
        $names = array_keys($definitions);

        $rows = PaymentGateaway::query()
            ->whereIn('name', $names)
            ->where('status', 1)
            ->get()
            ->keyBy('name');

        $methods = [];

        foreach ($names as $name) {
            if (! $rows->has($name)) {
                continue;
            }

            $gateway = $rows->get($name);
            if (! $gateway || ! $this->hasGatewayCredentials($gateway)) {
                continue;
            }

            $definition = $definitions[$name];
            $methods[] = [
                'key' => $name,
                'provider' => $definition['provider'],
                'method' => $definition['method'],
                'title' => $definition['title'],
                'description' => $definition['description'],
                'logo' => $definition['logo'],
                'client_id' => $this->resolveClientId($gateway),
            ];
        }

        return $methods;
    }

    private function resolveExtra(PaymentGateaway $gateway): array
    {
        if (! $this->columnExists('extra')) {
            return [];
        }

        $value = $gateway->extra;

        if (is_array($value)) {
            return $value;
        }

        if (is_string($value) && $value !== '') {
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $decoded;
            }
        }

        return [];
    }

    private function applyGatewayExtraDefaults(string $gatewayName, array $extra): array
    {
        $defaults = match ($gatewayName) {
            PaymentGateaway::NAME_BOG_ONLINE, PaymentGateaway::NAME_BOG_INSTALLMENT => [
                'base_url' => config('services.payments.bog_base_url', 'https://api.bog.ge'),
                'create_endpoint' => config('services.payments.bog_create_endpoint', '/payments/v1/ecommerce/orders'),
                'status_endpoint' => config('services.payments.bog_status_endpoint', '/payments/v1/receipt/{id}'),
                'token_url' => config('services.payments.bog_oauth_token_url'),
                'accept_language' => config('services.payments.bog_accept_language', 'en'),
            ],
            PaymentGateaway::NAME_TBC_ONLINE => [
                'base_url' => config('services.payments.tbc_base_url', 'https://pay.flitt.com'),
                'create_endpoint' => config('services.payments.tbc_create_endpoint', '/api/checkout/url'),
            ],
            PaymentGateaway::NAME_TBC_INSTALLMENT => [
                'base_url' => config('services.payments.tbc_installment_base_url', 'https://api.tbcbank.ge'),
                'create_endpoint' => config('services.payments.tbc_installment_create_endpoint', '/v1/online-installments/applications'),
                'token_url' => config('services.payments.tbc_oauth_token_url', 'https://api.tbcbank.ge/oauth/token'),
            ],
            PaymentGateaway::NAME_FLITT_ONLINE => [
                'base_url' => config('services.payments.flitt_base_url', 'https://pay.flitt.com'),
                'create_endpoint' => config('services.payments.flitt_create_endpoint', '/api/checkout/url'),
                'status_endpoint' => config('services.payments.flitt_status_endpoint', '/api/recurring/status/{id}'),
            ],
            default => [],
        };

        if ($defaults === []) {
            return $extra;
        }

        foreach ($defaults as $key => $value) {
            if ($value === null) {
                continue;
            }

            if (is_string($value) && trim($value) === '') {
                continue;
            }

            $extra[$key] = $value;
        }

        return $extra;
    }

    private function hasGatewayCredentials(PaymentGateaway $gateway): bool
    {
        $clientId = trim((string) ($this->resolveClientId($gateway) ?? ''));
        $clientSecret = trim((string) ($this->resolveClientSecret($gateway) ?? ''));

        if ($clientId === '' || $clientSecret === '') {
            return false;
        }

        if ((string) $gateway->name === PaymentGateaway::NAME_TBC_INSTALLMENT) {
            $merchantKey = trim((string) ($this->resolveMerchantKey($gateway, $this->resolveExtra($gateway)) ?? ''));
            return $merchantKey !== '';
        }

        return true;
    }

    private function resolveClientId(PaymentGateaway $gateway): ?string
    {
        return $this->columnExists('client') ? $gateway->client : ($gateway->client_id ?? null);
    }

    private function resolveClientSecret(PaymentGateaway $gateway): ?string
    {
        return $this->columnExists('secret') ? $gateway->secret : ($gateway->client_secret ?? null);
    }

    private function resolveMerchantKey(PaymentGateaway $gateway, array $extra): ?string
    {
        if ($this->columnExists('merchant_key')) {
            return $gateway->merchant_key;
        }

        $merchantKey = data_get($extra, 'merchant_key');
        return is_string($merchantKey) ? $merchantKey : null;
    }

    private function columnExists(string $column): bool
    {
        if ($this->schemaColumns === null) {
            $this->schemaColumns = Schema::hasTable('payment_gateaways')
                ? Schema::getColumnListing('payment_gateaways')
                : [];
        }

        return in_array($column, $this->schemaColumns, true);
    }
}
