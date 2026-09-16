<?php

namespace App\Services;


class CartService
{
    public function remove($rowId = false)
    {
        if ($rowId) {
            \Cart::remove($rowId);
        } else {
            \Cart::clear();
        }
    }
}
