<?php

namespace App\Services\Payments;

use App\Models\Payment;
use App\Services\Payments\DTO\VerifyResult;
use Illuminate\Support\Facades\DB;

class PaymentStateService
{
    public function applyVerificationResult(Payment $payment, VerifyResult $verifyResult): Payment
    {
        return DB::transaction(function () use ($payment, $verifyResult) {
            $payment = Payment::query()->with('order')->lockForUpdate()->findOrFail($payment->id);

            if (Payment::isFinalStatus($payment->status)) {
                return $payment;
            }

            $payment->status = $verifyResult->status;
            $payment->gateway_payment_id = $verifyResult->gatewayPaymentId ?: $payment->gateway_payment_id;
            $payment->raw_response = $verifyResult->raw;

            if ($verifyResult->status === Payment::STATUS_PAID) {
                $payment->paid_at = now();
                $payment->failed_at = null;
                $payment->canceled_at = null;

                $payment->order->pay_status = 4;
                $payment->order->transaction_id = $payment->gateway_payment_id;
                $payment->order->save();
            } elseif ($verifyResult->status === Payment::STATUS_FAILED) {
                $payment->failed_at = now();
                $payment->paid_at = null;
                $payment->canceled_at = null;

                if ((int) $payment->order->pay_status !== 4) {
                    $payment->order->pay_status = 0;
                    $payment->order->save();
                }
            } elseif ($verifyResult->status === Payment::STATUS_CANCELED) {
                $payment->canceled_at = now();
                $payment->paid_at = null;

                if ((int) $payment->order->pay_status !== 4) {
                    $payment->order->pay_status = 0;
                    $payment->order->save();
                }
            }

            $payment->save();

            return $payment;
        });
    }
}
