<?php

namespace Tests\Unit\Models;

use App\Models\Country;
use App\Models\Genre;
use App\Models\Language;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesLocaleTables;
use Tests\TestCase;

class LocalizedTaxonomyTest extends TestCase
{
    use CreatesLocaleTables;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->ensureLanguagesTable();
        $this->ensureCountriesTable();
        $this->ensureGenresTable();
    }

    public function test_genre_localized_name_honors_locale_and_fallback(): void
    {
        $genre = Genre::factory()->create([
            'name_translations' => [
                'en' => 'Action',
                'es' => 'Acción',
            ],
            'slug' => 'action',
            'tmdb_id' => 1,
        ]);

        $this->assertSame('Acción', $genre->localizedName('es'));
        $this->assertSame('Action', $genre->localizedName('fr'));

        app()->setLocale('es');
        $this->assertSame('Acción', $genre->name);

        app()->setLocale(config('app.fallback_locale'));
    }

    public function test_country_localized_name_honors_locale_and_fallback(): void
    {
        $country = Country::factory()->create([
            'name_translations' => [
                'en' => 'United States',
                'fr' => 'États-Unis',
            ],
            'code' => 'US',
        ]);

        $this->assertSame('États-Unis', $country->localizedName('fr'));
        $this->assertSame('United States', $country->localizedName('es'));
    }

    public function test_language_localized_native_name_honors_locale_and_fallback(): void
    {
        $language = Language::factory()->create([
            'name_translations' => [
                'en' => 'Spanish',
                'es' => 'Español',
            ],
            'native_name_translations' => [
                'es' => 'Español',
            ],
            'code' => 'ES01',
        ]);

        $this->assertSame('Español', $language->localizedName('es'));
        $this->assertSame('Spanish', $language->localizedName('fr'));
        $this->assertSame('Español', $language->localizedNativeName('es'));
        $this->assertSame('Español', $language->localizedNativeName('fr'));
    }
}
