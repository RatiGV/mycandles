<?php

namespace Tests\Feature\Payments;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class PaymentReturnTest extends TestCase
{
    public function test_payment_return_route_is_registered(): void
    {
        $this->assertTrue(Route::has('payments.return'));
        $this->assertTrue(Route::has('payments.webhook'));
    }

    public function test_return_route_contains_provider_parameter(): void
    {
        $route = app('router')->getRoutes()->getByName('payments.return');

        $this->assertNotNull($route);
        $this->assertSame('payments/return/{provider}', $route->uri());
    }

    public function test_return_callback_marks_payment_paid_skeleton(): void
    {
        $this->markTestIncomplete(
            'TODO: create pending payment fixture, mock gateway verifyReturn(), and assert PaymentStateService updates pay_status=4.'
        );
    }
}
