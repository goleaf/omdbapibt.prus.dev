<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    /**
     * Production country catalogue sourced from ISO 3166-1 alpha-2.
     *
     * @var array<int, array<string, mixed>>
     */
    public const COUNTRIES = [
        [
            'name' => 'United States',
            'code' => 'US',
            'active' => true,
        ],
        [
            'name' => 'United Kingdom',
            'code' => 'GB',
            'active' => true,
        ],
        [
            'name' => 'Canada',
            'code' => 'CA',
            'active' => true,
        ],
        [
            'name' => 'France',
            'code' => 'FR',
            'active' => true,
        ],
        [
            'name' => 'Germany',
            'code' => 'DE',
            'active' => true,
        ],
        [
            'name' => 'Japan',
            'code' => 'JP',
            'active' => true,
        ],
        [
            'name' => 'Australia',
            'code' => 'AU',
            'active' => true,
        ],
        [
            'name' => 'India',
            'code' => 'IN',
            'active' => true,
        ],
        [
            'name' => 'Spain',
            'code' => 'ES',
            'active' => true,
        ],
        [
            'name' => 'Brazil',
            'code' => 'BR',
            'active' => true,
        ],
    ];

    /**
     * Seed the application's production country catalogue.
     */
    public function run(): void
    {
        collect(self::COUNTRIES)->each(function (array $country): void {
            Country::query()->updateOrCreate(
                ['code' => $country['code']],
                $country,
            );
        });
    }
}
