<?php

namespace Database\Seeders;

use App\Models\Genre;
use Illuminate\Database\Seeder;

class GenreSeeder extends Seeder
{
    private const TARGET_COUNT = 1000;

    /**
     * Seed the application's genre catalogue with localized data.
     */
    public function run(): void
    {
        $current = Genre::query()->count();

        if ($current >= self::TARGET_COUNT) {
            return;
        }

        Genre::factory()->count(self::TARGET_COUNT - $current)->create();
    }
}
