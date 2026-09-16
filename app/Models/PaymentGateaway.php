<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentGateaway extends Model
{
    public const NAME_BOG_ONLINE = 'bog_online';
    public const NAME_BOG_INSTALLMENT = 'bog_installment';
    public const NAME_TBC_ONLINE = 'tbc_online';
    public const NAME_TBC_INSTALLMENT = 'tbc_installment';
    public const NAME_FLITT_ONLINE = 'flitt_online';

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'status' => 'boolean',
        ];
    }

    public static function checkoutDefinitions(): array
    {
        return [
            self::NAME_BOG_ONLINE => [
                'provider' => Payment::PROVIDER_BOG,
                'method' => Payment::METHOD_ONLINE_PAY,
                'title' => 'BOG Online Pay',
                'description' => 'Pay with card securely via Bank of Georgia.',
                'logo' => '/assets/images/bog_eng_vertical.png',
            ],
            self::NAME_TBC_ONLINE => [
                'provider' => Payment::PROVIDER_TBC,
                'method' => Payment::METHOD_ONLINE_PAY,
                'title' => 'TBC Online Pay',
                'description' => 'Pay with card securely via TBC Bank.',
                'logo' => '/assets/images/Tbc-logo-ka_GE.svg',
            ],
            self::NAME_BOG_INSTALLMENT => [
                'provider' => Payment::PROVIDER_BOG,
                'method' => Payment::METHOD_INSTALLMENT,
                'title' => 'BOG Installment',
                'description' => 'Split payment using BOG installment terms.',
                'logo' => '/assets/images/bog_eng_vertical.png',
            ],
            self::NAME_TBC_INSTALLMENT => [
                'provider' => Payment::PROVIDER_TBC,
                'method' => Payment::METHOD_INSTALLMENT,
                'title' => 'TBC Installment',
                'description' => 'Split payment using TBC installment terms.',
                'logo' => '/assets/images/Tbc-logo-ka_GE.svg',
            ]
        ];
    }

    public static function nameFor(string $provider, string $method): ?string
    {
        foreach (self::checkoutDefinitions() as $name => $definition) {
            if ($definition['provider'] === $provider && $definition['method'] === $method) {
                return $name;
            }
        }

        return null;
    }
}
