<?php

namespace Tests\Unit\Database\Factories;

use App\Models\Movie;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MovieFactoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_movie_factory_populates_expected_payload(): void
    {
        $movie = Movie::factory()->create();

        $this->assertSame('movie', $movie->media_type);
        $this->assertIsArray($movie->title);
        $this->assertArrayHasKey('en', $movie->title);
        $this->assertIsArray($movie->translation_metadata);
        $this->assertArrayHasKey('title', $movie->translation_metadata);
        $this->assertIsArray($movie->credits);
        $this->assertArrayHasKey('cast', $movie->credits);
        $this->assertIsArray($movie->streaming_links);
        $this->assertNotEmpty($movie->streaming_links);
        $this->assertIsArray($movie->trailers);
        $this->assertNotEmpty($movie->trailers);
    }
}
