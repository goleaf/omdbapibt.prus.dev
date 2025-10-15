<?php

namespace Tests\Unit\Database\Factories;

use App\Models\Genre;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class GenreFactoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_genre_factory_creates_persisted_genre(): void
    {
        $genre = Genre::factory()->create();

        $this->assertNotNull($genre->getKey());
        $this->assertNotEmpty($genre->name);
        $this->assertNotEmpty($genre->slug);
        $this->assertNotNull($genre->tmdb_id);
    }

    public function test_named_state_applies_provided_values(): void
    {
        $genre = Genre::factory()
            ->named('Science Fiction', 42)
            ->create();

        $this->assertSame('Science Fiction', $genre->name);
        $this->assertSame(Str::slug('Science Fiction'), $genre->slug);
        $this->assertSame(42, $genre->tmdb_id);
    }
}
