<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    public const PROVIDER_BOG = 'bog';
    public const PROVIDER_TBC = 'tbc';
    public const PROVIDER_FLITT = 'flitt';

    public const METHOD_ONLINE_PAY = 'online_pay';
    public const METHOD_INSTALLMENT = 'installment';

    public const STATUS_PENDING = 'pending';
    public const STATUS_PAID = 'paid';
    public const STATUS_FAILED = 'failed';
    public const STATUS_CANCELED = 'canceled';

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'raw_request' => 'array',
            'raw_response' => 'array',
            'paid_at' => 'datetime',
            'failed_at' => 'datetime',
            'canceled_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public static function isFinalStatus(string $status): bool
    {
        return in_array($status, [self::STATUS_PAID, self::STATUS_FAILED, self::STATUS_CANCELED], true);
    }
}
