<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TagSeeder extends Seeder
{
    public function run(): void
    {
        $path = __DIR__.'\tags.json';

        $tags = json_decode(file_get_contents($path), true) ?? [];

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('tags_translates')->truncate();
        DB::table('tags')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        foreach ($tags as $tag) {
            DB::table('tags')->insert([
                'id' => $tag['id'],
                'sort' => $tag['sort'],
                'status' => $tag['status'],
                'created_at' => $tag['created_at'],
                'updated_at' => $tag['updated_at'],
            ]);

            $translations = $tag['translations'] ?? [];
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
            $baseTranslation = $baseTranslation ?? ['title' => 'tag'];

            foreach (['en', 'ka'] as $lang) {
                $translation = null;
                foreach ($translations as $t) {
                    if (($t['lang'] ?? null) === $lang) {
                        $translation = $t;
                        break;
                    }
                }
                $translation = $translation ?? $baseTranslation;

                DB::table('tags_translates')->insert([
                    'parent_id' => $tag['id'],
                    'title' => $translation['title'],
                    'lang' => $lang,
                    'created_at' => $tag['created_at'],
                    'updated_at' => $tag['updated_at'],
                ]);
            }
        }
    }
}
