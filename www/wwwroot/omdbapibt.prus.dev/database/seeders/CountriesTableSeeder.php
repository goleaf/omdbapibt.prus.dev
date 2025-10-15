<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CountriesTableSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $countries = [
            ['code' => 'US', 'name' => 'United States', 'active' => true],
            ['code' => 'GB', 'name' => 'United Kingdom', 'active' => true],
            ['code' => 'CA', 'name' => 'Canada', 'active' => true],
            ['code' => 'AU', 'name' => 'Australia', 'active' => true],
            ['code' => 'FR', 'name' => 'France', 'active' => true],
            ['code' => 'DE', 'name' => 'Germany', 'active' => true],
            ['code' => 'ES', 'name' => 'Spain', 'active' => true],
            ['code' => 'IT', 'name' => 'Italy', 'active' => true],
            ['code' => 'JP', 'name' => 'Japan', 'active' => true],
            ['code' => 'KR', 'name' => 'South Korea', 'active' => true],
        ];

        $payload = collect($countries)->map(function (array $country) use ($now) {
            return array_merge($country, [
                'active' => $country['active'] ? 1 : 0,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        })->all();

        DB::table('countries')->upsert($payload, ['code'], ['name', 'active', 'updated_at']);
    }
}
