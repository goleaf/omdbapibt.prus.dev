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
}
