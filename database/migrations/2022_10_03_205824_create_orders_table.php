<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->integer('district_id')->nullable();
            $table->integer('user_id');
            $table->string('payment_type');
            $table->string('code');
            $table->string('sale_code')->nullable();
            $table->float('sale_percent')->nullable();
            $table->string('transaction_id')->nullable();
            $table->integer('pay_status');
            $table->integer('status');
            $table->string('address');
            $table->integer('delivery')->nullable();
            $table->float('delivery_price')->nullable();
            $table->float('total');
            $table->integer('year');
            $table->integer('month');
            $table->integer('day');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
