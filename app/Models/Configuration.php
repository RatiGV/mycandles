<?php

namespace App\Models;

use Artisan;
use Cache;
use DB;
use Illuminate\Database\Eloquent\Model;
use LaravelLocalization;

class Configuration extends Model
{
    public static function updateItem($request, $item)
    {
        $item->admin_lang = $request->admin_lang;
        $item->admin_color = $request->admin_color;

        $item->crop_images_for_this_tables = $request->crop_images_for_this_tables
                ? json_encode($request->crop_images_for_this_tables) : '[]';

        $item->moderator_admin_actions = $request->moderator_admin_actions
                ? json_encode($request->moderator_admin_actions) : '[]';

        $item->standard_admin_actions = $request->standard_admin_actions
                ? json_encode($request->standard_admin_actions) : '[]';

        $all_modules = [
            'sliders',
            'products',
            'product_categories',
            'brands',
            'news',
            'photo_galleries',
            'textpages',
            'informations',
            'seos',
            'sales',
        ];

        // თუ ადმინმა მონიშნა რომ სურს ქეშირებების გამოყენება
        if ($request->cache === 'on' && $request->cache_time && $request->module) {
            // ის მოდულები რომლებიც არ მონიშნა ადმინმა ქეშირების ველში
            foreach (array_diff($all_modules, $request->module) as $remove_from_cache) {
                Cache::forget($remove_from_cache);
                DB::table('caches')->where('module', $remove_from_cache)->delete();
            }

            $cache_times = $request->cache_time;

            foreach ($cache_times as $key => $cache_time) {
                if (! $cache_time) {
                    unset($cache_times[$key]);
                }
            }

            $cache_times = array_values($cache_times);

            if (count($request->module) !== count($cache_times)) {
                return false;
            }

            foreach ($request->module as $k => $module) {
                // სლაიდერის შენახვა ქეშში
                if ($module === 'sliders' && ! Cache::has('sliders')) {
                    DB::table('caches')->insert([
                        'module' => $module,
                        'minutes' => $cache_times[$k],
                        'expires_at' => date('Y-m-d H:i:s', time() + $cache_times[$k] * 60),
                    ]);

                    Cache::remember('sliders', $cache_times[$k] * 60, function () {

                        $sliders = [];

                        foreach (LaravelLocalization::getSupportedLocales() as $prefix => $lang) {
                            $sliders[$prefix] = Slider::allItems($prefix, $status_on = true);
                        }

                        return $sliders;
                    });
                }

                // პროდუქტის შენახვა ქეშში (პაგინაციის გარეშე)
                if ($module === 'products' && ! Cache::has('products')) {
                    DB::table('caches')->insert([
                        'module' => $module,
                        'minutes' => $cache_times[$k],
                        'expires_at' => date('Y-m-d H:i:s', time() + $cache_times[$k] * 60),
                    ]);

                    Cache::remember('products', $cache_times[$k] * 60, function () {

                        $products = [];

                        foreach (LaravelLocalization::getSupportedLocales() as $prefix => $lang) {
                            $products[$prefix] = Product::allItems($prefix, $status_on = true, $where_in = false, $where_in_cat = false, $paginate = false, $get = true);
                        }

                        foreach (LaravelLocalization::getSupportedLocales() as $prefix => $lang) {
                            foreach ($products[$prefix] as $product) {
                                $product->images = $product->images;
                            }
                        }

                        return $products;
                    });
                }

                // პროდუქტის კატეგორიების შენახვა ქეშში
                if ($module === 'product_categories' && ! Cache::has('product_categories')) {
                    DB::table('caches')->insert([
                        'module' => $module,
                        'minutes' => $cache_times[$k],
                        'expires_at' => date('Y-m-d H:i:s', time() + $cache_times[$k] * 60),
                    ]);

                    Cache::remember('product_categories', $cache_times[$k] * 60, function () {

                        $categories_by_langs = [];

                        foreach (LaravelLocalization::getSupportedLocales() as $prefix => $lang) {
                            $categories = ProductCategory::allItems($prefix, $level = false, $status_on = true, $where_in = false);

                            if ($categories->count()) {
                                $max_level = $categories->sortBy('level')->last()->level;

                                for ($level = $max_level; $level > 0; $level--) {
                                    $current_level_cats = $categories->where('level', $level);
                                    $next_level_cats = $categories->where('level', $level + 1);

                                    foreach ($current_level_cats as $current_level_cat) {
                                        $current_level_cat->childs = $next_level_cats->where('parent_id', $current_level_cat->id);
                                    }
                                }
                            }

                            $categories_by_langs[$prefix] = $categories;
                        }

                        return $categories_by_langs;
                    });
                }

                // ბრენდების შენახვა ქეშში
                if ($module === 'brands' && ! Cache::has('brands')) {
                    DB::table('caches')->insert([
                        'module' => $module,
                        'minutes' => $cache_times[$k],
                        'expires_at' => date('Y-m-d H:i:s', time() + $cache_times[$k] * 60),
                    ]);

                    Cache::remember('brands', $cache_times[$k] * 60, function () {

                        $brands = [];

                        foreach (LaravelLocalization::getSupportedLocales() as $prefix => $lang) {
                            $brands[$prefix] = Brand::allItems($prefix, $status_on = true);
                        }

                        return $brands;
                    });
                }

                // ფასდაკლების აქციის შენახვა ქეშში
                if ($module === 'sales' && ! Cache::has('sales')) {
                    DB::table('caches')->insert([
                        'module' => $module,
                        'minutes' => $cache_times[$k],
                        'expires_at' => date('Y-m-d H:i:s', time() + $cache_times[$k] * 60),
                    ]);

                    Cache::remember('sales', $cache_times[$k] * 60, function () {

                        $sales = [];

                        foreach (LaravelLocalization::getSupportedLocales() as $prefix => $lang) {
                            $sales[$prefix] = Sale::allItems($prefix, $status_on = true);
                        }

                        return $sales;
                    });
                }

                // სიახლეების შენახვა ქეშში
                if ($module === 'news' && ! Cache::has('news')) {
                    DB::table('caches')->insert([
                        'module' => $module,
                        'minutes' => $cache_times[$k],
                        'expires_at' => date('Y-m-d H:i:s', time() + $cache_times[$k] * 60),
                    ]);

                    Cache::remember('news', $cache_times[$k] * 60, function () {

                        $news = [];

                        foreach (LaravelLocalization::getSupportedLocales() as $prefix => $lang) {
                            $news[$prefix] = News::allItems($prefix, $status_on = true, $where_in = false);
                        }

                        return $news;
                    });
                }

                // ფოტოგალერიის შენახვა ქეშში
                if ($module === 'photo_galleries' && ! Cache::has('photo_galleries')) {
                    DB::table('caches')->insert([
                        'module' => $module,
                        'minutes' => $cache_times[$k],
                        'expires_at' => date('Y-m-d H:i:s', time() + $cache_times[$k] * 60),
                    ]);

                    Cache::remember('photo_galleries', $cache_times[$k] * 60, function () {

                        $photo_galleries = [];

                        foreach (LaravelLocalization::getSupportedLocales() as $prefix => $lang) {
                            $photo_galleries[$prefix] = PhotoGallery::allItems($prefix, $status_on = true);
                        }

                        foreach (LaravelLocalization::getSupportedLocales() as $prefix => $lang) {
                            foreach ($photo_galleries[$prefix] as $photo_gallery) {
                                $photo_gallery->images = $photo_gallery->images;
                            }
                        }

                        return $photo_galleries;
                    });
                }

                // ტექსტური გვერდების შენახვა ქეშში
                if ($module === 'textpages' && ! Cache::has('textpages')) {
                    DB::table('caches')->insert([
                        'module' => $module,
                        'minutes' => $cache_times[$k],
                        'expires_at' => date('Y-m-d H:i:s', time() + $cache_times[$k] * 60),
                    ]);

                    Cache::remember('textpages', $cache_times[$k] * 60, function () {

                        $textpages = [];

                        foreach (LaravelLocalization::getSupportedLocales() as $prefix => $lang) {
                            $textpages[$prefix] = Textpage::allItems($prefix);
                        }

                        return $textpages;
                    });
                }

                // საკონტაქტო ინფორმაციის შენახვა
                if ($module === 'informations' && ! Cache::has('informations')) {
                    DB::table('caches')->insert([
                        'module' => $module,
                        'minutes' => $cache_times[$k],
                        'expires_at' => date('Y-m-d H:i:s', time() + $cache_times[$k] * 60),
                    ]);

                    Cache::remember('informations', $cache_times[$k] * 60, function () {

                        $contact_info_by_langs = [];

                        foreach (LaravelLocalization::getSupportedLocales() as $prefix => $lang) {
                            $info = Information::getItemInfo(3, $prefix);
                            $contact_info_by_langs[$prefix] = $info;
                        }

                        return $contact_info_by_langs;
                    });
                }

                // SEO-ს შენახვა ქეშში
                if ($module === 'seos' && ! Cache::has('seos')) {
                    DB::table('caches')->insert([
                        'module' => $module,
                        'minutes' => $cache_times[$k],
                        'expires_at' => date('Y-m-d H:i:s', time() + $cache_times[$k] * 60),
                    ]);

                    Cache::remember('seos', $cache_times[$k] * 60, function () {

                        $seos = [];

                        foreach (LaravelLocalization::getSupportedLocales() as $prefix => $lang) {
                            $seos[$prefix] = Seo::allItems($prefix);
                        }

                        return $seos;
                    });
                }
            }
        } else {
            /*
             * თუ მთლიანი ქეშის გასუფთავება გვინდა
             */
            Artisan::call('cache:clear');
            DB::table('caches')->delete();

            /*
             *  გასუფთავდეს ქეშირებები მხოლოდ მოდულისათვის
             */
            /*
            foreach($all_modules as $module)
            {
                Cache::forget($module);
                DB::table('caches')->delete();
            }
             *
             */
        }

        return $item->update() ? true : false;
    }
}
