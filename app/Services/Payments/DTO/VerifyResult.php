<?php

namespace App\Services\Payments\DTO;

class VerifyResult
{
    public function __construct(
        public readonly bool $valid,
        public readonly string $status,
        public readonly ?string $gatewayPaymentId = null,
        public readonly array $raw = [],
        public readonly ?string $error = null,
    ) {
    }

    public static function valid(string $status, ?string $gatewayPaymentId = null, array $raw = []): self
    {
        return new self(
            valid: true,
            status: $status,
            gatewayPaymentId: $gatewayPaymentId,
            raw: $raw,
        );
    }

    public static function invalid(string $error, array $raw = [], string $status = 'pending'): self
    {
        return new self(
            valid: false,
            status: $status,
            error: $error,
            raw: $raw,
        );
    }
}
