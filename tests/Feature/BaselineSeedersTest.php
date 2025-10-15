<?php

namespace Tests\Feature;

use App\Models\Country;
use App\Models\Genre;
use App\Models\Language;
use Database\Seeders\CountrySeeder;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\GenreSeeder;
use Database\Seeders\LanguageSeeder;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class BaselineSeedersTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->ensureLanguagesTable();
        $this->ensureCountriesTable();
        $this->ensureGenresTable();

        DB::table('languages')->delete();
        DB::table('countries')->delete();
        DB::table('genres')->delete();
    }

    public function test_language_seeder_populates_baseline_catalogue(): void
    {
        $this->seed(LanguageSeeder::class);

        $this->assertDatabaseHas('languages', ['code' => 'en', 'name' => 'English']);
        $this->assertDatabaseHas('languages', ['code' => 'zh', 'name' => 'Chinese']);
        $this->assertSame(count(LanguageSeeder::LANGUAGES), Language::query()->count());
    }

    public function test_country_seeder_populates_production_countries(): void
    {
        $this->seed(CountrySeeder::class);

        $this->assertDatabaseHas('countries', ['code' => 'US', 'name' => 'United States']);
        $this->assertDatabaseHas('countries', ['code' => 'IN', 'name' => 'India']);
        $this->assertSame(count(CountrySeeder::COUNTRIES), Country::query()->count());
    }

    public function test_genre_seeder_syncs_tmdb_genres(): void
    {
        $this->seed(GenreSeeder::class);

        $this->assertDatabaseHas('genres', ['slug' => 'action', 'tmdb_id' => 28]);
        $this->assertDatabaseHas('genres', ['slug' => 'science-fiction', 'tmdb_id' => 878]);
        $this->assertSame(count(GenreSeeder::GENRES), Genre::query()->count());
    }

    public function test_database_seeder_runs_all_baseline_seeders(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->assertSame(count(LanguageSeeder::LANGUAGES), Language::query()->count());
        $this->assertSame(count(CountrySeeder::COUNTRIES), Country::query()->count());
        $this->assertSame(count(GenreSeeder::GENRES), Genre::query()->count());
    }

    protected function ensureLanguagesTable(): void
    {
        if (Schema::hasTable('languages')) {
            return;
        }

        Schema::create('languages', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->string('native_name')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    protected function ensureCountriesTable(): void
    {
        if (Schema::hasTable('countries')) {
            return;
        }

        Schema::create('countries', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    protected function ensureGenresTable(): void
    {
        if (Schema::hasTable('genres')) {
            return;
        }

        Schema::create('genres', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->unsignedBigInteger('tmdb_id')->nullable();
            $table->timestamps();
        });
    }
}
