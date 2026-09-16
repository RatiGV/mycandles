<?php

namespace Tests\Feature\Payments;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class PaymentInitiationTest extends TestCase
{
    public function test_payment_initiation_route_is_registered(): void
    {
        $this->assertTrue(Route::has('checkout.payment.initiate'));
    }

    public function test_guest_user_is_redirected_when_initiating_payment(): void
    {
        $route = app('router')->getRoutes()->getByName('checkout.payment.initiate');

        $this->assertNotNull($route);
        $this->assertContains('auth', $route->middleware());
    }

    public function test_initiation_happy_path_skeleton(): void
    {
        $this->markTestIncomplete(
            'TODO: seed payment_gateaways/products and mock gateway adapter to assert payment + redirect URL response.'
        );
    }
}
