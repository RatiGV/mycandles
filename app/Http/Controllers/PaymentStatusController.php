<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentStatusController extends Controller
{
    public function show(Request $request, Payment $payment): JsonResponse
    {
        if ((int) $payment->order->user_id !== (int) $request->user()->id) {
            abort(403);
        }

        return response()->json([
            'payment_id' => $payment->id,
            'status' => $payment->status,
            'redirect_url' => $payment->redirect_url,
            'updated_at' => optional($payment->updated_at)->toIso8601String(),
        ]);
    }
}
