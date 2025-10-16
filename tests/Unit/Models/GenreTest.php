<?php

namespace Tests\Unit\Models;

use App\Models\Genre;
use App\Models\Movie;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GenreTest extends TestCase
{
    use RefreshDatabase;

    public function test_movies_relationship(): void
    {
        $movie = Movie::factory()->create();
        $genre = Genre::factory()->create();

        $genre->movies()->attach($movie);

        $this->assertTrue($genre->movies->contains($movie));
    }

    public function test_localized_name_returns_translation(): void
    {
        $genre = Genre::factory()
            ->state([
                'name' => 'Mystery',
                'name_translations' => ['en' => 'Mystery', 'es' => 'Misterio'],
                'slug' => 'mystery',
            ])
            ->create();

        $this->assertSame('Misterio', $genre->localizedName('es'));
    }
}
