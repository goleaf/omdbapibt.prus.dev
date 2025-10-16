<?php

namespace Tests\Unit\Models;

use App\Models\Language;
use App\Models\Movie;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesLocaleTables;
use Tests\TestCase;

class LanguageTest extends TestCase
{
    use CreatesLocaleTables;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->ensureLanguagesTable();
    }

    public function test_active_flag_is_cast_to_boolean(): void
    {
        $language = Language::create([
            'name_translations' => ['en' => 'Spanish', 'es' => 'Español'],
            'code' => 'es',
            'native_name_translations' => ['en' => 'Spanish', 'es' => 'Español'],
            'active' => 0,
        ]);

        $this->assertFalse($language->fresh()->active);
    }

    public function test_movies_relationship_returns_attached_movies(): void
    {
        $movie = Movie::factory()->create();
        $language = Language::create([
            'name_translations' => ['en' => 'German', 'de' => 'Deutsch'],
            'code' => 'de',
            'native_name_translations' => ['en' => 'German', 'de' => 'Deutsch'],
            'active' => true,
        ]);

        $language->movies()->attach($movie);

        $this->assertTrue($language->movies->contains($movie));
    }

    public function test_localized_helpers_return_translated_values(): void
    {
        $language = Language::create([
            'name_translations' => ['en' => 'French', 'es' => 'Francés', 'fr' => 'Français'],
            'code' => 'fr',
            'native_name_translations' => ['en' => 'French', 'es' => 'Francés', 'fr' => 'Français'],
            'active' => true,
        ]);

        $this->assertSame('Francés', $language->localizedName('es'));
        $this->assertSame('Français', $language->localizedNativeName('fr'));
    }
}
