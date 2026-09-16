<?php
use App\Models\Information;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
return new class extends Migration
{
    /*
     * informations.pixel და informations.analytics ადრე სრულ <script> კოდს ინახავდა.
     * ახლა ინახება მხოლოდ იდენტიფიკატორი, ამიტომ არსებული ჩანაწერები გარდაიქმნება.
     */
    public function up(): void
    {
        if (! Schema::hasTable('informations')) {
            return;
        }
        $has_pixel = Schema::hasColumn('informations', 'pixel');
        $has_analytics = Schema::hasColumn('informations', 'analytics');
        if (! $has_pixel && ! $has_analytics) {
            return;
        }
        foreach (DB::table('informations')->get() as $row) {
            $values = [];
            if ($has_pixel) {
                $values['pixel'] = Information::extractPixelId($row->pixel ?? null);
            }
            if ($has_analytics) {
                $values['analytics'] = Information::extractAnalyticsId($row->analytics ?? null);
            }
            DB::table('informations')->where('id', $row->id)->update($values);
        }
    }
    public function down(): void
    {
        /* სრული <script> კოდის აღდგენა შეუძლებელია, ამიტომ დაბრუნება არ ხდება */
    }
};
