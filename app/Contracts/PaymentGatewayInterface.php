<?php

namespace App\Contracts;

use App\Models\Payment;
use App\Services\Payments\DTO\CreatePaymentResult;
use App\Services\Payments\DTO\StatusResult;
use App\Services\Payments\DTO\VerifyResult;
use Illuminate\Http\Request;

interface PaymentGatewayInterface
{
    public function createPayment(Payment $payment, array $gatewayConfig): CreatePaymentResult;

    public function verifyReturn(Request $request, array $gatewayConfig): VerifyResult;

    public function verifyWebhook(Request $request, array $gatewayConfig): VerifyResult;

    public function fetchPaymentStatus(string $gatewayPaymentId, array $gatewayConfig): StatusResult;
}
