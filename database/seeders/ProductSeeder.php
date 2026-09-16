<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $path = __DIR__.'\products.json';

        $products = json_decode(file_get_contents($path), true) ?? [];

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('products_translates')->truncate();
        DB::table('products')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $sort = 1;

        foreach ($products as $product) {
            DB::table('products')->insert([
                'id' => $product['id'],
                'code' => $product['sku'],
                'category_id' => $product['category_id'],
                'image' => $product['main_image'],
                'price' => $product['price'],
                'old_price' => $product['old_price'],
                'permalink' => $product['slug'],
                'sort' => $sort++,
                'available' => $product['stock'],
                'new_product' => 0,
                'status' => 1,
                'created_at' => $product['created_at'],
                'updated_at' => $product['updated_at'],
            ]);

            foreach (['en', 'ka'] as $lang) {
                DB::table('products_translates')->insert([
                    'parent_id' => $product['id'],
                    'title' => $product['title'],
                    'meta_title' => $product['meta_title'],
                    'alt' => $product['title'],
                    'description' => $product['long_description'],
                    'meta_description' => $product['meta_description'],
                    'lang' => $lang,
                    'created_at' => $product['created_at'],
                    'updated_at' => $product['updated_at'],
                ]);
            }
        }
    }
}
