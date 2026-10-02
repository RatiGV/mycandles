<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
return new class extends Migration
{
    /* informations.twitter ველი იცვლება informations.whatsapp ველით */
    public function up(): void
    {
        if (Schema::hasTable('informations') && Schema::hasColumn('informations', 'twitter') && ! Schema::hasColumn('informations', 'whatsapp')) {
            Schema::table('informations', function ($table) {
                $table->renameColumn('twitter', 'whatsapp');
            });
        }
    }
    public function down(): void
    {
        if (Schema::hasTable('informations') && Schema::hasColumn('informations', 'whatsapp') && ! Schema::hasColumn('informations', 'twitter')) {
            Schema::table('informations', function ($table) {
                $table->renameColumn('whatsapp', 'twitter');
            });
        }
    }
};
