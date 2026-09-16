<?php

namespace App\Services\Payments\Support;

use App\Models\Payment;

class PaymentStatusMapper
{
    public static function normalize(string|null $value): string
    {
        $status = strtolower(trim((string) $value));

        return match ($status) {
            'paid', 'success', 'succeeded', 'approved', 'completed', 'ok' => Payment::STATUS_PAID,
            'failed', 'declined', 'error' => Payment::STATUS_FAILED,
            'canceled', 'cancelled', 'void', 'reversed' => Payment::STATUS_CANCELED,
            default => Payment::STATUS_PENDING,
        };
    }
}
