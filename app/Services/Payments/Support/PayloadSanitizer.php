<?php

namespace App\Services\Payments\Support;

class PayloadSanitizer
{
    private const SENSITIVE_KEYS = [
        'secret',
        'client_secret',
        'password',
        'token',
        'access_token',
        'refresh_token',
        'authorization',
        'api_key',
    ];

    public static function sanitize(array $payload): array
    {
        $result = [];

        foreach ($payload as $key => $value) {
            $normalizedKey = is_string($key) ? strtolower($key) : (string) $key;

            if (in_array($normalizedKey, self::SENSITIVE_KEYS, true)) {
                $result[$key] = '[redacted]';
                continue;
            }

            if (is_array($value)) {
                $result[$key] = self::sanitize($value);
                continue;
            }

            $result[$key] = $value;
        }

        return $result;
    }
}
