<?php

namespace App\Models;

use App\Traits\ActionLog;
use DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Schema;
use Overtrue\LaravelFavorite\Traits\Favoriteable;

class Product extends Model
{
    use ActionLog, Favoriteable;

    protected $guarded = [];

    private static $current_class = __CLASS__;

    private static $translates_class = \App\Models\ProductsTranslate::class;

    private static $main_table = 'products';

    private static $translates_table = 'products_translates';

    private static $gallery_images_table = 'products_photos';

    public static function get_required_lang()
    {
        return DB::table('configurations')->select('admin_lang')->first()->admin_lang;
    }

    public function trans()
    {
        return $this->hasOne(ProductsTranslate::class, 'parent_id', 'id')->where('lang', locale());
    }

    public function images(): HasMany
    {
        return $this->hasMany(\App\Models\ProductsPhoto::class, 'parent_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(\App\Models\ProductCategory::class);
    }

    public function scopeWhereHasCategories(Builder $query, array $categoryIds): Builder
    {
        if (empty($categoryIds)) {
            return $query;
        }

        $table = $query->getModel()->getTable();

        return $query->where(function ($categoryQuery) use ($categoryIds, $table) {
            foreach ($categoryIds as $categoryId) {
                $categoryQuery->orWhereRaw(
                    "JSON_CONTAINS(REPLACE({$table}.category_id, ' ', ''), JSON_QUOTE(?))",
                    [(string) $categoryId]
                );
            }
        });
    }

    public static function addItem($request)
    {
        $class_base_name = class_basename(self::$current_class);
        $item = new self::$current_class;
        $table_columns = Schema::getColumnListing(self::$main_table);
        $request_keys = $request->except([
            '_token',
            'translates',
            'image',
            'status',
            'category_id',
            'slug',
            'new_product',
            'available',
        ]);

        $item->status = $request->status === 'on' ? 1 : 0;
        $item->available = $request->available === 'on' ? 1 : 0;
        $item->new_product = $request->new_product === 'on' ? 1 : 0;

        $item->category_id = $request->category_id ? json_encode($request->category_id) : '[]';
        
        
        
        if ($request->slug) {
            $getSlug = Product::where('slug', $request->slug)->first();

            $item->slug = $getSlug ? $getSlug->slug . '-1' : $request->slug;
        }

        $max_sort = self::$current_class::max('sort');
        $item->sort = $max_sort ? ++$max_sort : 1;

        foreach ($request_keys as $key => $value) {
            if (in_array($key, $table_columns)) {
                $item->$key = $value;
            }
        }

        $item->price = $request->price !== null && $request->price !== '' ? $request->price : 0;
        $item->code = $request->code !== null && $request->code !== '' ? $request->code : null;

        if ($request->hasFile('image')) {
            $data = [];
            $data['main_table'] = self::$main_table;
            $data['column_name'] = 'image';
            $data['upload_folder'] = $class_base_name;
            $data['item'] = $item;

            $upload_image = self::$current_class::uploadFile($request, $data);
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

            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    $upload_gallery_image = self::$current_class::uploadGalleryImage($request, $file, $item, $class_base_name, self::$gallery_images_table);
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
        $request_keys = $request->except([
            '_token',
            'translates',
            'image',
            'status',
            'category_id',
            'slug',
            'new_product',
            'available',
        ]);
        $item->status = $request->status === 'on' ? 1 : 0;
        $item->new_product = $request->new_product === 'on' ? 1 : 0;
        $item->available = $request->available === 'on' ? 1 : 0;
        $item->category_id = $request->category_id ? json_encode($request->category_id) : '[]';

        if ($request->slug) {
            $getSlug = Product::where('slug', $request->slug)->first();

            $item->slug = $getSlug && $getSlug->id !== $item->id ? $request->slug . '-1' : $request->slug;
        }

        foreach ($request_keys as $key => $value) {
            if (in_array($key, $table_columns)) {
                $item->$key = $value;
            }
        }

        $item->price = $request->price !== null && $request->price !== '' ? $request->price : 0;
        $item->code = $request->code !== null && $request->code !== '' ? $request->code : null;

        if ($request->hasFile('image')) {
            /*
            * როდესაც public_html საქაღალდეში მხოლოდ საჯარო ფაილებია,
            * საიტის ლოგიკა, კონფიგურაციული ფაილები და ა.შ კი მის გარეთ
            *
            */
            if (! is_null($item->image)) {
                if (file_exists('../public_html' . $item->image)) {
                    unlink('../public_html' . $item->image);
                }
            }

            /*
            // როდესაც მთლიანი საიტი public_html საქაღალდეშია
            if(file_exists(public_path($item->image)))
            {
               unlink(public_path($item->image));
            }
            *
            */

            $data = [];
            $data['main_table'] = self::$main_table;
            $data['column_name'] = 'image';
            $data['upload_folder'] = $class_base_name;
            $data['item'] = $item;

            $upload_image = self::$current_class::uploadFile($request, $data);
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

            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    $upload_gallery_image = self::$current_class::uploadGalleryImage($request, $file, $item, $class_base_name, self::$gallery_images_table);
                }
            }
            $item::storeLog($item, __CLASS__, 'Update');

            return true;
        }

        return false;
    }

    public static function getItemInfo($id = 0, $local = '', $status_on = false)
    {
        if (property_exists(__CLASS__, 'translates_table')) {
            return self::$current_class::join(self::$translates_table, self::$main_table . '.id', '=', self::$translates_table . '.parent_id')
                ->leftjoin('product_category_translates AS pct', function ($join) {
                    $join->on(DB::raw("JSON_UNQUOTE(JSON_EXTRACT(REPLACE(" . self::$main_table . ".category_id, ' ', ''), '$[0]'))"), '=', 'pct.parent_id')
                        ->where('pct.lang', '=', 'ka');
                })
                ->where(self::$main_table . '.id', $id)
                ->where(self::$translates_table . '.lang', $local)
                ->select(
                    self::$main_table . '.*',
                    self::$translates_table . '.title',
                    self::$translates_table . '.meta_title',
                    self::$translates_table . '.alt',
                    self::$translates_table . '.description',
                    self::$translates_table . '.short_description',
                    self::$translates_table . '.meta_description',
                    'pct.title AS cat_title',
                )
                ->when($status_on, function ($query, $status_on) {
                    return $query->where(self::$main_table . '.status', $status_on);
                })
                ->first();
        } else {
            return self::$current_class::where(self::$main_table . '.id', $id)->first();
        }
    }

    public static function allItems($local = '', $status_on = false, $where_in = false, $where_in_cat = false, $paginate = false, $get = false)
    {
        return self::$current_class::join(self::$translates_table, self::$main_table . '.id', self::$translates_table . '.parent_id')
            ->leftjoin('product_category_translates AS pct', function ($join) {
                $join->on(DB::raw("JSON_UNQUOTE(JSON_EXTRACT(REPLACE(" . self::$main_table . ".category_id, ' ', ''), '$[0]'))"), '=', 'pct.parent_id')
                    ->where('pct.lang', '=', 'ka');
            })
            ->where(self::$translates_table . '.lang', $local)
            ->select(
                self::$main_table . '.*',
                self::$translates_table . '.title',
                self::$translates_table . '.meta_title',
                self::$translates_table . '.alt',
                self::$translates_table . '.description',
                self::$translates_table . '.short_description',
                self::$translates_table . '.meta_description',
                'pct.title AS cat_title'
            )
            ->when($status_on, function ($query, $status_on) {
                return $query->where(self::$main_table . '.status', $status_on);
            })
            ->when($where_in, function ($query, $where_in) {
                return $query->whereIn(self::$main_table . '.id', $where_in);
            })
            ->when($where_in_cat, function ($query, $where_in_cat) {
                return $query->whereIn(self::$main_table . '.category_id', $where_in_cat);
            })
            ->orderBy('id', 'DESC')
            ->when($get, function ($query, $get) {
                return $query->get();
            })
            ->when($paginate, function ($query, $paginate) {
                return $query->paginate(12);
            });
    }
}
