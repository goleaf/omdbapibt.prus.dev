<?php

namespace Tests\Feature\Movie;

use App\Models\Country;
use App\Models\Language;
use App\Models\Movie;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class MovieLocaleRelationshipsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->ensureLanguagesTable();
        $this->ensureCountriesTable();
    }

    public function test_movie_languages_relationship_persists_records(): void
    {
        $movie = Movie::factory()->create();

        $english = Language::query()->create([
            'name' => 'English',
            'name_translations' => ['en' => 'English', 'es' => 'Inglés'],
            'native_name' => 'English',
            'native_name_translations' => ['en' => 'English', 'es' => 'Inglés'],
            'code' => 'en',
        ]);

        $spanish = Language::query()->create([
            'name' => 'Spanish',
            'name_translations' => ['en' => 'Spanish', 'es' => 'Español'],
            'native_name' => 'Español',
            'native_name_translations' => ['en' => 'Spanish', 'es' => 'Español'],
            'code' => 'es',
        ]);

        $movie->languages()->attach([$english->id, $spanish->id]);

        $attachedLanguageIds = $movie->languages()->pluck('languages.id')->all();

        $this->assertEqualsCanonicalizing([$english->id, $spanish->id], $attachedLanguageIds);
        $this->assertDatabaseHas('movie_language', [
            'movie_id' => $movie->id,
            'language_id' => $english->id,
        ]);
        $this->assertDatabaseHas('movie_language', [
            'movie_id' => $movie->id,
            'language_id' => $spanish->id,
        ]);
    }

    public function test_movie_countries_relationship_persists_records(): void
    {
        $movie = Movie::factory()->create();

        $usa = Country::query()->create([
            'name' => 'United States',
            'name_translations' => ['en' => 'United States', 'es' => 'Estados Unidos'],
            'code' => 'US',
        ]);

        $japan = Country::query()->create([
            'name' => 'Japan',
            'name_translations' => ['en' => 'Japan', 'es' => 'Japón'],
            'code' => 'JP',
        ]);

        $movie->countries()->attach([$usa->id, $japan->id]);

        $attachedCountryIds = $movie->countries()->pluck('countries.id')->all();

        $this->assertEqualsCanonicalizing([$usa->id, $japan->id], $attachedCountryIds);
        $this->assertDatabaseHas('movie_country', [
            'movie_id' => $movie->id,
            'country_id' => $usa->id,
        ]);
        $this->assertDatabaseHas('movie_country', [
            'movie_id' => $movie->id,
            'country_id' => $japan->id,
        ]);
    }

    private function ensureLanguagesTable(): void
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

    private function ensureCountriesTable(): void
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
}
