<?php

namespace App\Services\Payments\Builders;

use App\Models\Order;

class InstallmentPayloadBuilder
{
    public function build(Order $order): array
    {
        return [
            'items' => $order->products->map(function ($item) {
                return [
                    'sku' => (string) $item->product_id,
                    'title' => (string) $item->product_id,
                    'price' => (float) $item->unit_price,
                    'quantity' => (int) $item->qty,
                ];
            })->values()->all(),
            'customer' => [
                'phone' => optional($order->user)->phone,
                'name' => optional($order->user)->name,
            ],
            'shipping' => [
                'address' => $order->address,
                'district_id' => $order->district_id,
            ],
        ];
    }
}
