<?php

namespace Tests\Feature;

use App\Models\Movie;
use App\Models\Person;
use App\Models\TvShow;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ParserCommandsTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_persists_movies_from_the_configured_payload(): void
    {
        $payload = [[
            'tmdb_id' => 100,
            'imdb_id' => 'tt1234567',
            'omdb_id' => Str::uuid()->toString(),
            'slug' => 'example-movie',
            'title' => 'Example Movie',
            'original_title' => 'Example Movie',
            'year' => 2024,
            'runtime' => 120,
            'release_date' => '2024-10-12',
            'plot' => 'An illustrative payload for the parser test.',
            'tagline' => 'Testing is believing.',
            'homepage' => 'https://example.com/movie',
            'budget' => 100000000,
            'revenue' => 250000000,
            'status' => 'Released',
            'popularity' => 98.123,
            'vote_average' => 8.5,
            'vote_count' => 1250,
            'poster_path' => '/poster.jpg',
            'backdrop_path' => '/backdrop.jpg',
            'trailer_url' => 'https://youtube.com/watch?v=example',
            'media_type' => 'movie',
            'adult' => false,
            'video' => false,
        ]];

        config()->set('parser.movies', $payload);

        $this->artisan('movie:parse-new')
            ->expectsOutput('Stored 1 record.')
            ->assertSuccessful();

        $this->assertDatabaseHas('movies', [
            'tmdb_id' => 100,
            'title' => 'Example Movie',
            'slug' => 'example-movie',
        ]);
    }

    #[Test]
    public function it_refreshes_existing_records_instead_of_creating_duplicates(): void
    {
        $movie = Movie::factory()->create([
            'tmdb_id' => 101,
            'title' => 'Legacy Title',
            'slug' => 'legacy-title',
        ]);

        config()->set('parser.movies', [[
            'tmdb_id' => 101,
            'title' => 'Updated Title',
            'slug' => 'updated-title',
        ]]);

        $this->artisan('movie:parse-new')->assertSuccessful();

        $this->assertDatabaseHas('movies', [
            'id' => $movie->id,
            'title' => 'Updated Title',
            'slug' => 'updated-title',
        ]);
        $this->assertSame(1, Movie::count());
    }

    #[Test]
    public function it_persists_tv_and_people_payloads(): void
    {
        config()->set('parser.tv_shows', [[
            'tmdb_id' => 201,
            'imdb_id' => 'tt7654321',
            'slug' => 'example-series',
            'name' => 'Example Series',
            'popularity' => 87.921,
        ]]);

        config()->set('parser.people', [[
            'tmdb_id' => 301,
            'imdb_id' => 'nm1234567',
            'name' => 'Example Person',
            'popularity' => 45.8,
        ]]);

        $this->artisan('tv:parse-new')->assertSuccessful();
        $this->artisan('people:parse-new')->assertSuccessful();

        $this->assertDatabaseHas('tv_shows', [
            'tmdb_id' => 201,
            'name' => 'Example Series',
        ]);

        $this->assertDatabaseHas('people', [
            'tmdb_id' => 301,
            'name' => 'Example Person',
        ]);
    }
}
