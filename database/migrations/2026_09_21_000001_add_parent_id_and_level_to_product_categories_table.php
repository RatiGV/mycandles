<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('product_categories')) {
            return;
        }
        Schema::table('product_categories', function (Blueprint $table) {
            if (! Schema::hasColumn('product_categories', 'parent_id')) {
                $table->unsignedBigInteger('parent_id')->nullable()->after('id');
            }
            if (! Schema::hasColumn('product_categories', 'level')) {
                $table->unsignedInteger('level')->default(1)->after('parent_id');
            }
        });
    }
    public function down(): void
    {
        if (! Schema::hasTable('product_categories')) {
            return;
        }
        Schema::table('product_categories', function (Blueprint $table) {
            if (Schema::hasColumn('product_categories', 'level')) {
                $table->dropColumn('level');
            }
            if (Schema::hasColumn('product_categories', 'parent_id')) {
                $table->dropColumn('parent_id');
            }
        });
    }
};
