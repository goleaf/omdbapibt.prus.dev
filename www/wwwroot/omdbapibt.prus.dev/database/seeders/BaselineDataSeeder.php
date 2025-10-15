<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class BaselineDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            LanguagesTableSeeder::class,
            CountriesTableSeeder::class,
            GenresTableSeeder::class,
        ]);
    }
}
