<?php

namespace App\Models;

use App\Traits\ActionLog;
use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Schema;

class Information extends Model
{
    use ActionLog;

    protected $fillable = ['image'];

    protected $table = 'informations';

    private static $current_class = __CLASS__;

    private static $translates_class = \App\Models\InformationsTranslate::class;

    private static $main_table = 'informations';

    private static $translates_table = 'informations_translates';

    public function translate(): HasOne
    {
        return $this->hasOne(InformationsTranslate::class, 'parent_id', 'id')->where('lang', locale());
    }

    public static function get_required_lang()
    {
        return DB::table('configurations')->select('admin_lang')->first()->admin_lang;
    }

    public static function updateItem($request, $item)
    {
        $class_base_name = class_basename(self::$current_class);
        $table_columns = Schema::getColumnListing(self::$main_table);
        $request_keys = $request->except([
            '_token',
            'translates',
            'logo',
            'logo_for_admin',
            'top_banner',
            'bottom_banner',
            'favicon',
            'login_bg',
            'status',
        ]);

        foreach ($request_keys as $key => $value) {
            if (in_array($key, $table_columns)) {
                $item->$key = $value;
            }
        }

        if ($request->hasFile('logo')) {
            $destination = 'uploads/informations';
            $extension = $request->file('logo')->getClientOriginalExtension();
            $fileName = mt_rand(11111, 99999) . time() . '.' . $extension;
            $request->file('logo')->move($destination, $fileName);
            $image_src = '/uploads/informations/' . $fileName;
            $item->logo = $image_src;
        }

        if ($request->hasFile('top_banner')) {
            $destination = 'uploads/informations';
            $extension = $request->file('top_banner')->getClientOriginalExtension();
            $fileName = mt_rand(11111, 99999) . time() . '.' . $extension;
            $request->file('top_banner')->move($destination, $fileName);
            $image_src = '/uploads/informations/' . $fileName;
            $item->top_banner = $image_src;
        }

        if ($request->hasFile('bottom_banner')) {
            $destination = 'uploads/informations';
            $extension = $request->file('bottom_banner')->getClientOriginalExtension();
            $fileName = mt_rand(11111, 99999) . time() . '.' . $extension;
            $request->file('bottom_banner')->move($destination, $fileName);
            $image_src = '/uploads/informations/' . $fileName;
            $item->bottom_banner = $image_src;
        }

        if ($request->hasFile('logo_for_admin')) {
            $destination = 'uploads/informations';
            $extension = $request->file('logo_for_admin')->getClientOriginalExtension();
            $fileName = mt_rand(11111, 99999) . time() . '.' . $extension;
            $request->file('logo_for_admin')->move($destination, $fileName);
            $image_src = '/uploads/informations/' . $fileName;
            $item->logo_for_admin = $image_src;
        }

        if ($request->hasFile('favicon')) {
            $destination = 'uploads/informations';
            $extension = $request->file('favicon')->getClientOriginalExtension();
            $fileName = mt_rand(11111, 99999) . time() . '.' . $extension;
            $request->file('favicon')->move($destination, $fileName);
            $image_src = '/uploads/informations/' . $fileName;
            $item->favicon = $image_src;
        }

        if ($request->hasFile('login_bg')) {
            $destination = 'uploads/informations';
            $extension = $request->file('login_bg')->getClientOriginalExtension();
            $fileName = mt_rand(11111, 99999) . time() . '.' . $extension;
            $request->file('login_bg')->move($destination, $fileName);
            $image_src = '/uploads/informations/' . $fileName;
            $item->login_bg = $image_src;
        }

        if ($item->update()) {
            if (property_exists(__CLASS__, 'translates_table')) {
                $translates = $request->translates;
                $translates_table_columns = Schema::getColumnListing(self::$translates_table);

                foreach ($translates as $lang => $translation_data) {
                    $item_translate = self::$translates_class::where('parent_id', $item->id)->where('lang', $lang)->first();

                    foreach ($translation_data as $k => $v) {
                        /*  თუ რედაქტირების შაბლონში აღწერილია ისეთი ველი, რომლის 'name'
                         *  ატრიბუტის  შესაბამისი ველიც არ გვხვდება თარგმანების ცხრილში
                         */
                        if (! in_array($k, $translates_table_columns)) {
                            continue;
                        }

                        if (! $v) {
                            $item_translate->$k = $translates[self::get_required_lang()][$k];
                        } else {
                            $item_translate->$k = $v;
                        }
                    }

                    $item_translate->update();
                }
            }
            $item::storeLog($item, __CLASS__, 'Update');

            return true;
        }

        return false;
    }

    public static function getItemInfo($id = 0, $local = '')
    {
        if (property_exists(__CLASS__, 'translates_table')) {
            return self::$current_class::join(self::$translates_table, self::$main_table . '.id', self::$translates_table . '.parent_id')
                ->where(self::$main_table . '.id', $id)
                ->where(self::$translates_table . '.lang', $local)
                ->select(
                    self::$main_table . '.*',
                    self::$translates_table . '.title',
                    self::$translates_table . '.address',
                    self::$translates_table . '.slogan'
                )
                ->first();
        } else {
            return self::$current_class::where(self::$main_table . '.id', $id)->first();
        }
    }
}
