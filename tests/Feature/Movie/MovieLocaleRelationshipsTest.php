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
            'name_translations' => [
                'en' => 'English',
            ],
            'native_name_translations' => [
                'en' => 'English',
            ],
            'code' => 'EN01',
        ]);

        $spanish = Language::query()->create([
            'name_translations' => [
                'en' => 'Spanish',
                'es' => 'Español',
            ],
            'native_name_translations' => [
                'es' => 'Español',
            ],
            'code' => 'ES01',
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
            'name_translations' => [
                'en' => 'United States',
            ],
            'code' => 'US',
        ]);

        $japan = Country::query()->create([
            'name_translations' => [
                'en' => 'Japan',
                'ja' => '日本',
            ],
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
            $table->json('name_translations');
            $table->json('native_name_translations');
            $table->string('code', 5)->unique();
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
            $table->string('code', 2)->unique();
            $table->json('name_translations');
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }
}
