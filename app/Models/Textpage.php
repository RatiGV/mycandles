<?php

namespace App\Models;

use App\Traits\ActionLog;
use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Schema;

class Textpage extends Model
{
    use ActionLog;

    protected $fillable = ['image'];

    private static $current_class = __CLASS__;

    private static $translates_class = \App\Models\TextpagesTranslate::class;

    private static $main_table = 'textpages';

    private static $translates_table = 'textpages_translates';

    private static $gallery_images_table = 'textpages_photos';

    public static function get_required_lang()
    {
        return DB::table('configurations')->select('admin_lang')->first()->admin_lang;
    }

    public function images(): HasMany
    {
        return $this->hasMany(\App\Models\TextpagesPhoto::class, 'parent_id');
    }

    public function videos(): HasMany
    {
        return $this->hasMany(\App\Models\TextpagesVideo::class, 'parent_id')->orderBy('id', 'DESC');
    }

    public static function addItem($request)
    {
        $class_base_name = class_basename(self::$current_class);
        $item = new self::$current_class;
        $table_columns = Schema::getColumnListing(self::$main_table);
        $request_keys = $request->except(['_token', 'translates', 'image', 'status']);

        foreach ($request_keys as $key => $value) {
            if (in_array($key, $table_columns)) {
                $item->$key = $value;
            }
        }

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
        $request_keys = $request->except(['_token', 'translates', 'image', 'status', 'videos']);

        foreach ($request_keys as $key => $value) {
            if (in_array($key, $table_columns)) {
                $item->$key = $value;
            }
        }

        if ($request->has('videos')) {
            foreach ($request->videos as $video) {
                if ($video) {
                    DB::table('textpages_videos')->insert(
                        ['parent_id' => $item->id, 'url' => $video]
                    );
                }
            }
        }

        if ($request->hasFile('image')) {
            /*
            * როდესაც public_html საქაღალდეში მხოლოდ საჯარო ფაილებია,
            * საიტის ლოგიკა, კონფიგურაციული ფაილები და ა.შ კი მის გარეთ
            *
            */
            if (file_exists('../public_html'.$item->image)) {
                unlink('../public_html'.$item->image);
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

    public static function getItemInfo($id = 0, $local = '')
    {
        if (property_exists(__CLASS__, 'translates_table')) {
            return self::$current_class::join(self::$translates_table, self::$main_table.'.id', '=', self::$translates_table.'.parent_id')
                ->where(self::$main_table.'.id', $id)
                ->where(self::$translates_table.'.lang', $local)
                ->select(
                    self::$main_table.'.*',
                    self::$translates_table.'.title',
                    self::$translates_table.'.description',
                    self::$translates_table.'.meta_description',
                    self::$translates_table.'.meta_title'
                )
                ->first();
        } else {
            return self::$current_class::where(self::$main_table.'.id', $id)->first();
        }

    }

    public static function allItems($local = '')
    {
        if (property_exists(__CLASS__, 'translates_table')) {
            return self::$current_class::join(self::$translates_table, self::$main_table.'.id', '=', self::$translates_table.'.parent_id')
                ->where(self::$translates_table.'.lang', $local)
                ->select(self::$main_table.'.*', self::$translates_table.'.title')
                ->orderBy('id', 'asc')
                ->get();
        } else {
            return self::$current_class::select('*')
                ->when($status_on, function ($query, $status_on) {
                    return $query->where(self::$main_table.'.status', $status_on);
                })
                ->orderBy('id', 'asc')
                ->get();
        }
    }
}
