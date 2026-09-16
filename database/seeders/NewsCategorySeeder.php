<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NewsCategorySeeder extends Seeder
{
    public function run(): void
    {
        $path = __DIR__.'\news_categories.json';

        $categories = json_decode(file_get_contents($path), true) ?? [];

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('news_categories_translates')->truncate();
        DB::table('news_categories')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        foreach ($categories as $category) {
            DB::table('news_categories')->insert([
                'id' => $category['id'],
                'permalink' => $category['permalink'],
                'sort' => $category['sort'],
                'status' => $category['status'],
                'created_at' => $category['created_at'],
                'updated_at' => $category['updated_at'],
            ]);

            $translations = $category['translations'] ?? [];
            $baseTranslation = null;
            foreach ($translations as $translation) {
                if (($translation['lang'] ?? null) === 'en') {
                    $baseTranslation = $translation;
                    break;
                }
            }
            if (!$baseTranslation && $translations) {
                $baseTranslation = $translations[0];
            }
            $baseTranslation = $baseTranslation ?? [
                'title' => $category['permalink'],
            ];

            foreach (['en', 'ka'] as $lang) {
                $translation = null;
                foreach ($translations as $t) {
                    if (($t['lang'] ?? null) === $lang) {
                        $translation = $t;
                        break;
                    }
                }
                $translation = $translation ?? $baseTranslation;

                DB::table('news_categories_translates')->insert([
                    'parent_id' => $category['id'],
                    'title' => $translation['title'],
                    'lang' => $lang,
                    'created_at' => $category['created_at'],
                    'updated_at' => $category['updated_at'],
                ]);
            }
        }
    }
}
