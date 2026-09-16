<?php

namespace App\Models;


use App\Traits\ActionLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    use ActionLog;

    protected $guarded = ['id'];
    private static $current_class = __CLASS__;
    private static $translates_class = 'App\Models\FaqsTranslate';
    private static $main_table = 'faqs';
    private static $translates_table = 'faqs_translates';

    public static function get_required_lang()
    {
        return DB::table('configurations')->select('admin_lang')->first()->admin_lang;
    }

    public static function addItem($request)
    {
        $class_base_name = class_basename(self::$current_class);
        $item = new self::$current_class;
        $table_columns = Schema::getColumnListing(self::$main_table);
        $request_keys = $request->except(['_token', 'translates', 'status']);

        $item->status = $request->status === 'on' ? 1 : 0;
        $max_sort = self::$current_class::max('sort');
        $item->sort = $max_sort ? ++$max_sort : 1;

        foreach ($request_keys as $key => $value) {
            if (in_array($key, $table_columns)) {
                $item->$key = $value;
            }
        }

        if ($item->save()) {
            if (property_exists(__CLASS__, 'translates_table')) {
                // თარგმანების შემცველი ასცოციაციური მასივი ინდექსებით ka,en,ru ...
                $translates = $request->translates;
                // თარგმანების ცხრილის ველები
                $translates_table_columns = Schema::getColumnListing(self::$translates_table);

                foreach ($translates as $lang => $translation_data) {
                    $item_translate = new self::$translates_class();

                    /*
                     *  უშუალოდ თარგმანების მასივი [ველის_დასახელება => თარგმანი_შესაბამის_ენაზე]
                     *  $k : ველის დასახელება
                     *  $v : თარგმანი შესაბამის ენაზე
                     */
                    foreach ($translation_data as $k => $v) {
                        /*  თუ დამატების შაბლონში აღწერილია ისეთი ველი, რომლის 'name'
                         *  ატრიბუტის  შესაბამისი ველიც არ გვხვდება თარგმანების ცხრილში
                         */
                        if (!in_array($k, $translates_table_columns)) {
                            continue;
                        }

                        // თუ რომელიმე სათარგმნი ველი არ შეიყვანა აუცილებელი ენის გარდა რომელიმე სხვა ენაზე
                        if (!$v) {
                            // არაკრეფილის მნიშვნელობად ჩაჯდეს აუცილებელი ენის მნიშვნელობა
                            $item_translate->$k = $translates[self::get_required_lang()][$k];
                        } else {
                            $item_translate->$k = $v;
                        }
                    }

                    $item_translate->lang = $lang;
                    $item_translate->parent_id = $item->id;
                    $item_translate->save();
                }
            }
            $item::storeLog($item, __CLASS__, 'Create');
            return true;
        }
        return false;
    }

    public static function updateItem($request, $item)
    {
        $class_base_name = class_basename(self::$current_class);
        $table_columns = Schema::getColumnListing(self::$main_table);
        $request_keys = $request->except(['_token', 'translates', 'status']);

        $item->status = $request->status === 'on' ? 1 : 0;

        foreach ($request_keys as $key => $value) {
            if (in_array($key, $table_columns)) {
                $item->$key = $value;
            }
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
                        if (!in_array($k, $translates_table_columns)) {
                            continue;
                        }

                        if (!$v) {
                            $item_translate->$k = $translates[self::get_required_lang()][$k];
                        } else {
                            $item_translate->$k = $v;
                        }
                    }

                    $item_translate->update();
                }
            }
            $item::storeLog($item, __CLASS__, 'Updated');
            return true;
        }
        return false;
    }

    public static function getItemInfo($id = 0, $local = '', $status_on = false)
    {
        if (property_exists(__CLASS__, 'translates_table')) {
            return self::$current_class::join(self::$translates_table, self::$main_table . '.id', '=', self::$translates_table . '.parent_id')
                ->where(self::$main_table . '.id', $id)
                ->where(self::$translates_table . '.lang', $local)
                ->select(
                    self::$main_table . '.*',
                    self::$translates_table . '.question',
                    self::$translates_table . '.answer'
                )
                ->when($status_on, function ($query, $status_on) {
                    return $query->where(self::$main_table . '.status', $status_on);
                })
                ->first();
        } else {
            return self::$current_class::where(self::$main_table . '.id', $id)->first();
        }
    }

    public static function allItems($local = '', $status_on = false)
    {
        if (property_exists(__CLASS__, 'translates_table')) {
            return self::$current_class::join(self::$translates_table, self::$main_table . '.id', '=', self::$translates_table . '.parent_id')
                ->where(self::$translates_table . '.lang', $local)
                ->select(
                    self::$main_table . '.*',
                    self::$translates_table . '.question',
                    self::$translates_table . '.answer'
                )
                ->when($status_on, function ($query, $status_on) {
                    return $query->where(self::$main_table . '.status', $status_on);
                })
                ->orderBy('sort', 'asc')
                ->get();
        } else {
            return self::$current_class::select('*')
                ->when($status_on, function ($query, $status_on) {
                    return $query->where(self::$main_table . '.status', $status_on);
                })
                ->orderBy('sort', 'asc')
                ->get();
        }
    }
}
