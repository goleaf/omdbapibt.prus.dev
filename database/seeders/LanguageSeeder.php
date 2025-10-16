<?php

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Seeder;

class LanguageSeeder extends Seeder
{
    private const TARGET_COUNT = 1000;

    /**
     * Seed the application's language catalogue with localized data.
     */
    public function run(): void
    {
        $current = Language::query()->count();

        if ($current >= self::TARGET_COUNT) {
            return;
        }

        Language::factory()->count(self::TARGET_COUNT - $current)->create();
    }
}
