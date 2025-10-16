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
            'name_translations' => [
                'en' => 'Spanish',
                'es' => 'Español',
                'fr' => 'Espagnol',
            ],
            'code' => 'ES01',
            'native_name_translations' => [
                'en' => 'Spanish',
                'es' => 'Español',
            ],
            'active' => 0,
        ]);

        $this->assertFalse($language->fresh()->active);
    }

    public function test_movies_relationship_returns_attached_movies(): void
    {
        $movie = Movie::factory()->create();
        $language = Language::create([
            'name_translations' => [
                'en' => 'German',
                'es' => 'Alemán',
            ],
            'code' => 'DE01',
            'native_name_translations' => [
                'en' => 'German',
                'de' => 'Deutsch',
            ],
            'active' => true,
        ]);

        $language->movies()->attach($movie);

        $this->assertTrue($language->movies->contains($movie));
    }
}
