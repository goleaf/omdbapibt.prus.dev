<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            LanguageSeeder::class,
            CountrySeeder::class,
            GenreSeeder::class,
        ]);

        if (! Schema::hasTable('users')) {
            return;
        }

        $this->call([
            UserSeeder::class,
            PersonSeeder::class,
            MovieSeeder::class,
            TvShowSeeder::class,
            UiTranslationSeeder::class,
            ReviewSeeder::class,
            WatchHistorySeeder::class,
            ParserEntrySeeder::class,
            ParserEntryHistorySeeder::class,
            AdminAuditLogSeeder::class,
            UserManagementLogSeeder::class,
        ]);
    }
}
