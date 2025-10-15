<?php

namespace Tests\Feature\Parser;

use App\Models\Movie;
use App\Models\TvShow;
use App\Services\Parser\MediaUpsertService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MediaUpsertServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_generates_dedup_hash_and_updates_existing_movie(): void
    {
        $service = new MediaUpsertService();

        $movie = $service->upsertMovie([
            'tmdb_id' => 123456,
            'imdb_id' => 'tt1234567',
            'title' => 'The Original Title',
            'slug' => 'the-original-title',
        ]);

        $expectedHash = md5('imdb_id:tt1234567|tmdb_id:123456');

        $this->assertSame($expectedHash, $movie->dedup_hash);
        $this->assertDatabaseHas('movies', [
            'id' => $movie->id,
            'dedup_hash' => $expectedHash,
        ]);

        $updated = $service->upsertMovie([
            'tmdb_id' => 123456,
            'imdb_id' => 'tt1234567',
            'title' => 'The Updated Title',
            'slug' => 'the-updated-title',
        ]);

        $this->assertSame($movie->id, $updated->id);
        $this->assertDatabaseCount('movies', 1);
        $this->assertSame('The Updated Title', $updated->fresh()->title);
        $this->assertSame('the-updated-title', $updated->fresh()->slug);
    }

    public function test_it_handles_unique_conflicts_when_upserting_movies(): void
    {
        $service = new MediaUpsertService();

        $payload = [
            'tmdb_id' => 98765,
            'imdb_id' => 'tt0098765',
            'title' => 'Primary Title',
            'slug' => 'primary-title',
        ];

        $expectedHash = md5('imdb_id:tt0098765|tmdb_id:98765');

        $conflictInserted = false;

        Movie::creating(function (Movie $model) use (&$conflictInserted, $payload, $expectedHash): void {
            if ($conflictInserted) {
                return;
            }

            $conflictInserted = true;

            Movie::withoutEvents(function () use ($payload, $expectedHash): void {
                Movie::create([
                    'tmdb_id' => $payload['tmdb_id'],
                    'imdb_id' => $payload['imdb_id'],
                    'dedup_hash' => $expectedHash,
                    'title' => 'Conflicting Title',
                    'slug' => 'conflicting-slug',
                ]);
            });
        });

        $result = $service->upsertMovie($payload);

        $this->assertDatabaseCount('movies', 1);
        $this->assertSame($expectedHash, $result->dedup_hash);
        $this->assertSame('Primary Title', $result->fresh()->title);
        $this->assertSame('primary-title', $result->fresh()->slug);
    }

    public function test_it_upserts_tv_shows_using_dedup_hash(): void
    {
        $service = new MediaUpsertService();

        $tvShow = $service->upsertTvShow([
            'tmdb_id' => 1234,
            'imdb_id' => 'tt7654321',
            'name' => 'Original Show',
            'slug' => 'original-show',
        ]);

        $expectedHash = md5('imdb_id:tt7654321|tmdb_id:1234');

        $this->assertSame($expectedHash, $tvShow->dedup_hash);

        $updated = $service->upsertTvShow([
            'tmdb_id' => 1234,
            'imdb_id' => 'tt7654321',
            'name' => 'Updated Show',
            'slug' => 'updated-show',
        ]);

        $this->assertSame($tvShow->id, $updated->id);
        $this->assertDatabaseCount('tv_shows', 1);
        $this->assertSame('Updated Show', $updated->fresh()->name);
    }
}
