<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LanguagesTableSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $languages = [
            ['code' => 'en', 'name' => 'English', 'native_name' => 'English', 'active' => true],
            ['code' => 'es', 'name' => 'Spanish', 'native_name' => 'Español', 'active' => true],
            ['code' => 'fr', 'name' => 'French', 'native_name' => 'Français', 'active' => true],
            ['code' => 'de', 'name' => 'German', 'native_name' => 'Deutsch', 'active' => true],
            ['code' => 'ja', 'name' => 'Japanese', 'native_name' => '日本語', 'active' => true],
            ['code' => 'zh', 'name' => 'Chinese', 'native_name' => '中文', 'active' => true],
            ['code' => 'pt', 'name' => 'Portuguese', 'native_name' => 'Português', 'active' => true],
            ['code' => 'ru', 'name' => 'Russian', 'native_name' => 'Русский', 'active' => true],
            ['code' => 'it', 'name' => 'Italian', 'native_name' => 'Italiano', 'active' => true],
            ['code' => 'ko', 'name' => 'Korean', 'native_name' => '한국어', 'active' => true],
        ];

        $payload = collect($languages)->map(function (array $language) use ($now) {
            return array_merge($language, [
                'active' => $language['active'] ? 1 : 0,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        })->all();

        DB::table('languages')->upsert($payload, ['code'], ['name', 'native_name', 'active', 'updated_at']);
    }
}
