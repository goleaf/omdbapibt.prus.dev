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

        $this->assertDatabaseHas('languages', [
            'code' => 'l0001',
            'name' => 'Language 0001',
        ]);

        $this->assertDatabaseHas('languages', [
            'code' => 'l0100',
            'name_translations->es' => 'Idioma 0100',
        ]);

        $this->assertSame(LanguageSeeder::TOTAL_LANGUAGES, Language::query()->count());
    }

    public function test_country_seeder_populates_production_countries(): void
    {
        $this->seed(CountrySeeder::class);

        $this->assertDatabaseHas('countries', [
            'code' => '00',
            'name' => 'Country 0001',
        ]);

        $this->assertDatabaseHas('countries', [
            'code' => '0A',
            'name_translations->fr' => 'Pays 0011',
        ]);

        $this->assertSame(CountrySeeder::TOTAL_COUNTRIES, Country::query()->count());
    }

    public function test_genre_seeder_syncs_tmdb_genres(): void
    {
        $this->seed(GenreSeeder::class);

        $this->assertDatabaseHas('genres', [
            'slug' => 'genre-0001',
            'tmdb_id' => 20001,
            'name' => 'Genre 0001',
        ]);

        $this->assertDatabaseHas('genres', [
            'slug' => 'genre-0100',
            'name_translations->es' => 'Género 0100',
        ]);

        $this->assertSame(GenreSeeder::TOTAL_GENRES, Genre::query()->count());
    }

    public function test_language_seeder_is_idempotent(): void
    {
        $this->seed(LanguageSeeder::class);
        $this->seed(LanguageSeeder::class);

        $this->assertSame(LanguageSeeder::TOTAL_LANGUAGES, Language::query()->count());
        $this->assertSame(
            Language::query()->pluck('code')->unique()->count(),
            Language::query()->count(),
        );
    }

    public function test_country_seeder_is_idempotent(): void
    {
        $this->seed(CountrySeeder::class);
        $this->seed(CountrySeeder::class);

        $this->assertSame(CountrySeeder::TOTAL_COUNTRIES, Country::query()->count());
        $this->assertSame(
            Country::query()->pluck('code')->unique()->count(),
            Country::query()->count(),
        );
    }

    public function test_genre_seeder_is_idempotent(): void
    {
        $this->seed(GenreSeeder::class);
        $this->seed(GenreSeeder::class);

        $this->assertSame(GenreSeeder::TOTAL_GENRES, Genre::query()->count());
        $this->assertSame(
            Genre::query()->pluck('slug')->unique()->count(),
            Genre::query()->count(),
        );
    }

    public function test_database_seeder_runs_all_baseline_seeders(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->assertSame(LanguageSeeder::TOTAL_LANGUAGES, Language::query()->count());
        $this->assertSame(CountrySeeder::TOTAL_COUNTRIES, Country::query()->count());
        $this->assertSame(GenreSeeder::TOTAL_GENRES, Genre::query()->count());
    }

    public function test_database_seeder_is_idempotent(): void
    {
        $this->seed(DatabaseSeeder::class);
        $this->seed(DatabaseSeeder::class);

        $this->assertSame(LanguageSeeder::TOTAL_LANGUAGES, Language::query()->count());
        $this->assertSame(CountrySeeder::TOTAL_COUNTRIES, Country::query()->count());
        $this->assertSame(GenreSeeder::TOTAL_GENRES, Genre::query()->count());
    }

    protected function ensureLanguagesTable(): void
    {
        if (Schema::hasTable('languages')) {
            return;
        }

        Schema::create('languages', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->json('name_translations')->nullable();
            $table->string('code')->unique();
            $table->string('native_name')->nullable();
            $table->json('native_name_translations')->nullable();
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
            $table->json('name_translations')->nullable();
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
            $table->json('name_translations')->nullable();
            $table->string('slug')->unique();
            $table->unsignedBigInteger('tmdb_id')->nullable();
            $table->timestamps();
        });
    }
}
