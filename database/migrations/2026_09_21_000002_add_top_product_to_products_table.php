<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('products')) {
            return;
        }
        Schema::table('products', function (Blueprint $table) {
            if (! Schema::hasColumn('products', 'top_product')) {
                $table->boolean('top_product')->default(0)->after('new_product');
            }
        });
    }
    public function down(): void
    {
        if (! Schema::hasTable('products')) {
            return;
        }
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'top_product')) {
                $table->dropColumn('top_product');
            }
        });
    }
};
