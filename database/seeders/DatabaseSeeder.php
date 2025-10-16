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

        if (Schema::hasTable('users')) {
            $this->call(UserSeeder::class);
        }

        if (Schema::hasTable('people')) {
            $this->call(PersonSeeder::class);
        }

        if (Schema::hasTable('movies')) {
            $this->call(MovieSeeder::class);
        }

        if (Schema::hasTable('tv_shows')) {
            $this->call(TvShowSeeder::class);
        }

        if (Schema::hasTable('ui_translations')) {
            $this->call(UiTranslationSeeder::class);
        }

        if (Schema::hasTable('reviews')) {
            $this->call(ReviewSeeder::class);
        }

        if (Schema::hasTable('watch_histories')) {
            $this->call(WatchHistorySeeder::class);
        }

        if (Schema::hasTable('parser_entries')) {
            $this->call(ParserEntrySeeder::class);
        }

        if (Schema::hasTable('parser_entry_histories')) {
            $this->call(ParserEntryHistorySeeder::class);
        }

        if (Schema::hasTable('admin_audit_logs')) {
            $this->call(AdminAuditLogSeeder::class);
        }

        if (Schema::hasTable('user_management_logs')) {
            $this->call(UserManagementLogSeeder::class);
        }
    }
}
