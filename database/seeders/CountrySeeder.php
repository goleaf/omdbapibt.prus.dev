<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    private const TARGET_COUNT = 1000;

    /**
     * Seed the application's production country catalogue with localized data.
     */
    public function run(): void
    {
        $current = Country::query()->count();

        if ($current >= self::TARGET_COUNT) {
            return;
        }

        Country::factory()->count(self::TARGET_COUNT - $current)->create();
    }
}
