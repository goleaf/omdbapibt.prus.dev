<?php

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Seeder;

class LanguageSeeder extends Seeder
{
    /**
     * Baseline language catalogue sourced from ISO 639-1.
     *
     * @var array<int, array<string, mixed>>
     */
    public const LANGUAGES = [
        [
            'name' => 'English',
            'native_name' => 'English',
            'code' => 'en',
            'active' => true,
        ],
        [
            'name' => 'Spanish',
            'native_name' => 'Español',
            'code' => 'es',
            'active' => true,
        ],
        [
            'name' => 'French',
            'native_name' => 'Français',
            'code' => 'fr',
            'active' => true,
        ],
        [
            'name' => 'German',
            'native_name' => 'Deutsch',
            'code' => 'de',
            'active' => true,
        ],
        [
            'name' => 'Italian',
            'native_name' => 'Italiano',
            'code' => 'it',
            'active' => true,
        ],
        [
            'name' => 'Japanese',
            'native_name' => '日本語',
            'code' => 'ja',
            'active' => true,
        ],
        [
            'name' => 'Korean',
            'native_name' => '한국어',
            'code' => 'ko',
            'active' => true,
        ],
        [
            'name' => 'Portuguese',
            'native_name' => 'Português',
            'code' => 'pt',
            'active' => true,
        ],
        [
            'name' => 'Russian',
            'native_name' => 'Русский',
            'code' => 'ru',
            'active' => true,
        ],
        [
            'name' => 'Chinese',
            'native_name' => '中文',
            'code' => 'zh',
            'active' => true,
        ],
    ];

    /**
     * Seed the application's language catalogue.
     */
    public function run(): void
    {
        collect(self::LANGUAGES)->each(function (array $language): void {
            Language::query()->updateOrCreate(
                ['code' => $language['code']],
                $language,
            );
        });
    }
}
