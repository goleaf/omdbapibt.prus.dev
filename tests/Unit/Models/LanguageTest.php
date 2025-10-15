<?php

namespace Tests\Unit\Models;

use App\Models\Language;
use App\Models\Movie;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesLocaleTables;
use Tests\TestCase;

class LanguageTest extends TestCase
{
    use RefreshDatabase;
    use CreatesLocaleTables;

    protected function setUp(): void
    {
        parent::setUp();

        $this->ensureLanguagesTable();
    }

    public function test_active_flag_is_cast_to_boolean(): void
    {
        $language = Language::create([
            'name' => 'Spanish',
            'code' => 'es',
            'native_name' => 'Español',
            'active' => 0,
        ]);

        $this->assertFalse($language->fresh()->active);
    }

    public function test_movies_relationship_returns_attached_movies(): void
    {
        $movie = Movie::factory()->create();
        $language = Language::create([
            'name' => 'German',
            'code' => 'de',
            'native_name' => 'Deutsch',
            'active' => true,
        ]);

        $language->movies()->attach($movie);

        $this->assertTrue($language->movies->contains($movie));
    }
}
