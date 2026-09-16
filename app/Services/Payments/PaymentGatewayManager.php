<?php

namespace App\Services\Payments;

use App\Contracts\PaymentGatewayInterface;
use App\Models\Payment;
use App\Services\Payments\Gateways\BogGateway;
use App\Services\Payments\Gateways\FlittGateway;
use App\Services\Payments\Gateways\TbcGateway;
use InvalidArgumentException;

class PaymentGatewayManager
{
    public function __construct(
        private readonly TbcGateway $tbcGateway,
        private readonly BogGateway $bogGateway,
        private readonly FlittGateway $flittGateway,
    ) {
    }

    public function getGateway(string $provider): PaymentGatewayInterface
    {
        return match ($provider) {
            Payment::PROVIDER_TBC => $this->tbcGateway,
            Payment::PROVIDER_BOG => $this->bogGateway,
            Payment::PROVIDER_FLITT => $this->flittGateway,
            default => throw new InvalidArgumentException("Unsupported gateway provider [{$provider}]."),
        };
    }
}
