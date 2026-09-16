<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('payment_gateaways')) {
            return;
        }

        if (! Schema::hasColumn('payment_gateaways', 'merchant_key')) {
            Schema::table('payment_gateaways', function (Blueprint $table) {
                $table->string('merchant_key')->nullable()->after('secret');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('payment_gateaways')) {
            return;
        }

        if (Schema::hasColumn('payment_gateaways', 'merchant_key')) {
            Schema::table('payment_gateaways', function (Blueprint $table) {
                $table->dropColumn('merchant_key');
            });
        }
    }
};
