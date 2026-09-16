<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NewsSeeder extends Seeder
{
        public function run(): void
    {
        $path = __DIR__.'\news.json';

        $items = json_decode(file_get_contents($path), true) ?? [];

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('news_translates')->truncate();
        DB::table('news')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $sort = 1;

        foreach ($items as $item) {
            DB::table('news')->insert([
                'id' => $item['id'],
                'category_id' => $item['category_id'],
                'tag_ids' => $item['tag_ids'],
                'image' => $item['image'],
                'permalink' => $item['permalink'],
                'sort' => $sort++,
                'status' => $item['status'],
                'created_at' => $item['created_at'],
                'updated_at' => $item['updated_at'],
            ]);

            foreach (['en', 'ka'] as $lang) {
                DB::table('news_translates')->insert([
                    'parent_id' => $item['id'],
                    'title' => $item['title'],
                    'meta_title' => $item['meta_title'],
                    'alt' => $item['alt'],
                    'short_description' => $item['description'],
                    'description' => $item['description'],
                    'meta_description' => $item['meta_description'],
                    'lang' => $lang,
                    'created_at' => $item['created_at'],
                    'updated_at' => $item['updated_at'],
                ]);
            }
        }
    }
}
