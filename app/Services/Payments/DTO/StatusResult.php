<?php

namespace App\Services\Payments\DTO;

class StatusResult
{
    public function __construct(
        public readonly bool $success,
        public readonly string $status,
        public readonly array $raw = [],
        public readonly ?string $error = null,
    ) {
    }

    public static function success(string $status, array $raw = []): self
    {
        return new self(success: true, status: $status, raw: $raw);
    }

    public static function failure(string $error, array $raw = []): self
    {
        return new self(success: false, status: 'pending', error: $error, raw: $raw);
    }
}
