<?php

namespace App\Services\Payments\DTO;

class CreatePaymentResult
{
    public function __construct(
        public readonly bool $success,
        public readonly ?string $redirectUrl = null,
        public readonly ?string $gatewayPaymentId = null,
        public readonly array $raw = [],
        public readonly ?string $error = null,
    ) {
    }

    public static function success(string $redirectUrl, ?string $gatewayPaymentId = null, array $raw = []): self
    {
        return new self(
            success: true,
            redirectUrl: $redirectUrl,
            gatewayPaymentId: $gatewayPaymentId,
            raw: $raw,
        );
    }

    public static function failure(string $error, array $raw = []): self
    {
        return new self(
            success: false,
            error: $error,
            raw: $raw,
        );
    }
}
