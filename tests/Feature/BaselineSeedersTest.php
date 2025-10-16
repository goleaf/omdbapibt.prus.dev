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
    private const TARGET_COUNT = 1000;

    protected function setUp(): void
    {
        parent::setUp();

        $this->ensureLanguagesTable();
        $this->ensureCountriesTable();
        $this->ensureGenresTable();
        $this->ensureUsersTable();

        DB::table('languages')->delete();
        DB::table('countries')->delete();
        DB::table('genres')->delete();
    }

    public function test_language_seeder_populates_multilingual_catalogue(): void
    {
        $this->seed(LanguageSeeder::class);

        $this->assertDatabaseCount('languages', self::TARGET_COUNT);
        $language = Language::query()->first();

        $this->assertNotNull($language);
        $this->assertArrayHasKey('en', $language->name_translations);
        $this->assertArrayHasKey('es', $language->name_translations);
        $this->assertArrayHasKey('fr', $language->name_translations);
        $this->assertArrayHasKey('en', $language->native_name_translations);
    }

    public function test_country_seeder_populates_multilingual_catalogue(): void
    {
        $this->seed(CountrySeeder::class);

        $this->assertDatabaseCount('countries', self::TARGET_COUNT);
        $country = Country::query()->first();

        $this->assertNotNull($country);
        $this->assertArrayHasKey('en', $country->name_translations);
        $this->assertArrayHasKey('es', $country->name_translations);
        $this->assertArrayHasKey('fr', $country->name_translations);
    }

    public function test_genre_seeder_populates_multilingual_catalogue(): void
    {
        $this->seed(GenreSeeder::class);

        $this->assertDatabaseCount('genres', self::TARGET_COUNT);
        $genre = Genre::query()->first();

        $this->assertNotNull($genre);
        $this->assertArrayHasKey('en', $genre->name_translations);
        $this->assertArrayHasKey('es', $genre->name_translations);
        $this->assertArrayHasKey('fr', $genre->name_translations);
    }

    public function test_language_seeder_is_idempotent(): void
    {
        $this->seed(LanguageSeeder::class);
        $this->seed(LanguageSeeder::class);

        $this->assertDatabaseCount('languages', self::TARGET_COUNT);
        $this->assertSame(
            Language::query()->pluck('code')->unique()->count(),
            Language::query()->count(),
        );
    }

    public function test_country_seeder_is_idempotent(): void
    {
        $this->seed(CountrySeeder::class);
        $this->seed(CountrySeeder::class);

        $this->assertDatabaseCount('countries', self::TARGET_COUNT);
        $this->assertSame(
            Country::query()->pluck('code')->unique()->count(),
            Country::query()->count(),
        );
    }

    public function test_genre_seeder_is_idempotent(): void
    {
        $this->seed(GenreSeeder::class);
        $this->seed(GenreSeeder::class);

        $this->assertDatabaseCount('genres', self::TARGET_COUNT);
        $this->assertSame(
            Genre::query()->pluck('slug')->unique()->count(),
            Genre::query()->count(),
        );
    }

    public function test_database_seeder_runs_all_baseline_seeders(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->assertDatabaseCount('languages', self::TARGET_COUNT);
        $this->assertDatabaseCount('countries', self::TARGET_COUNT);
        $this->assertDatabaseCount('genres', self::TARGET_COUNT);
    }

    public function test_database_seeder_is_idempotent(): void
    {
        $this->seed(DatabaseSeeder::class);
        $this->seed(DatabaseSeeder::class);

        $this->assertDatabaseCount('languages', self::TARGET_COUNT);
        $this->assertDatabaseCount('countries', self::TARGET_COUNT);
        $this->assertDatabaseCount('genres', self::TARGET_COUNT);
    }

    protected function ensureLanguagesTable(): void
    {
        if (Schema::hasTable('languages')) {
            return;
        }

        Schema::create('languages', function (Blueprint $table): void {
            $table->id();
            $table->json('name_translations');
            $table->json('native_name_translations');
            $table->string('code', 5)->unique();
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
            $table->string('code', 2)->unique();
            $table->json('name_translations');
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
            $table->unsignedBigInteger('tmdb_id')->nullable();
            $table->string('slug')->unique();
            $table->json('name_translations');
            $table->timestamps();
        });
    }

    protected function ensureUsersTable(): void
    {
        if (Schema::hasTable('users')) {
            return;
        }

        Schema::create('users', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->string('role')->default('user');
            $table->string('preferred_locale')->nullable();
            $table->timestamps();
        });
    }
}
