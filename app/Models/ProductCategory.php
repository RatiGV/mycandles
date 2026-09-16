<?php

namespace App\Models;

use App\Traits\ActionLog;
use DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Schema;

class ProductCategory extends Model
{
    use ActionLog;

    protected $fillable = ['image'];

    private static $current_class = __CLASS__;

    private static $translates_class = \App\Models\ProductCategoryTranslate::class;

    private static $main_table = 'product_categories';

    private static $translates_table = 'product_category_translates';

    private static $products = 'products';

    private static $tr_prods = 'products_translates';

    private static $products_photos = 'products_photos';

    public static function get_required_lang()
    {
        return DB::table('configurations')->select('admin_lang')->first()->admin_lang;
    }

    public function trans()
    {
        return $this->hasOne(ProductCategoryTranslate::class, 'parent_id', 'id')->where('lang', locale());
    }

    public function products()
    {
        return $this->hasMany(\App\Models\Product::class, 'id', 'id');
    }

    public function scopeWithProductsCount(Builder $query): Builder
    {
        $productTable = (new Product())->getTable();
        $categoryTable = $this->getTable();

        $jsonContains = "
            JSON_CONTAINS(
                REPLACE({$productTable}.category_id, ' ', ''),
                JSON_QUOTE(CAST({$categoryTable}.id AS CHAR))
            )
        ";

        return $query
            ->select("{$categoryTable}.*")
            ->selectSub(function ($subQuery) use ($productTable, $jsonContains) {
                $subQuery->from($productTable)
                    ->selectRaw('COUNT(*)')
                    ->where('status', 1)
                    ->whereRaw($jsonContains);
            }, 'products_count');
    }

    public static function addItem($request)
    {
        $class_base_name = class_basename(self::$current_class);
        $item = new self::$current_class;
        $table_columns = Schema::getColumnListing(self::$main_table);
        $request_keys = $request->except(['_token', 'translates', 'status']);

        $item->status = $request->status === 'on' ? 1 : 0;

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
                        if (! in_array($k, $translates_table_columns)) {
                            continue;
                        }

                        // თუ რომელიმე სათარგმნი ველი არ შეიყვანა აუცილებელი ენის გარდა რომელიმე სხვა ენაზე
                        if (! $v) {
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

        if ($request->parent_id > 0) {
            $item->parent_id = $request->parent_id;
            $parents_level = DB::table(self::$main_table)->where('id', $request->parent_id)->first()->level;
            $item->level = ++$parents_level;
        } else {
            $item->parent_id = null;
            $item->level = 1;
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
            return self::$current_class::join(self::$translates_table, self::$main_table . '.id', '=', self::$translates_table . '.parent_id')
                ->where(self::$main_table . '.id', $id)
                ->where(self::$translates_table . '.lang', $local)
                ->select(
                    self::$main_table . '.*',
                    self::$translates_table . '.title'
                )
                ->first();
        } else {
            return self::$current_class::where(self::$main_table . '.id', $id)->first();
        }
    }

    public static function allItems($local = '', $status_on = false, $where_in = false)
    {
        if (property_exists(__CLASS__, 'translates_table')) {
            return self::$current_class::join(self::$translates_table, self::$main_table . '.id', '=', self::$translates_table . '.parent_id')
                ->where(self::$translates_table . '.lang', $local)
                ->select(
                    self::$main_table . '.*',
                    self::$translates_table . '.title'
                )
                ->when($status_on, function ($query, $status_on) {
                    return $query->where(self::$main_table . '.status', $status_on);
                })
                ->when($where_in, function ($query, $where_in) {
                    return $query->whereIn(self::$main_table . '.id', $where_in);
                })
                ->orderBy('id', 'asc')
                ->get();
        } else {
            return self::$current_class::select('*')
                ->when($status_on, function ($query, $status_on) {
                    return $query->where(self::$main_table . '.status', $status_on);
                })
                ->orderBy('id', 'asc')
                ->get();
        }
    }
}
