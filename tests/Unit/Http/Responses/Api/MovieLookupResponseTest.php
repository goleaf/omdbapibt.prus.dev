<?php

namespace Tests\Unit\Http\Responses\Api;

use App\Http\Responses\Api\MovieLookupResponse;
use App\Models\Movie;
use Illuminate\Http\JsonResponse;
use Tests\TestCase;

class MovieLookupResponseTest extends TestCase
{
    public function test_serializes_movie_collection_with_metadata(): void
    {
        $firstMovie = Movie::factory()->make([
            'title' => ['en' => 'Dune'],
            'original_title' => 'Dune',
            'imdb_id' => 'tt1160419',
            'tmdb_id' => 438631,
            'slug' => 'dune-2021',
            'year' => 2021,
        ]);
        $firstMovie->setAttribute('id', 1);

        $secondMovie = Movie::factory()->make([
            'title' => ['en' => 'Arrival'],
            'original_title' => 'Arrival',
            'imdb_id' => 'tt2543164',
            'tmdb_id' => 329865,
            'slug' => 'arrival-2016',
            'year' => 2016,
        ]);
        $secondMovie->setAttribute('id', 2);

        $movies = collect([$firstMovie, $secondMovie]);

        $response = MovieLookupResponse::fromCollection($movies, 'science fiction', 10);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame([
            'data' => [
                [
                    'id' => 1,
                    'title' => ['en' => 'Dune'],
                    'original_title' => 'Dune',
                    'imdb_id' => 'tt1160419',
                    'tmdb_id' => 438631,
                    'slug' => 'dune-2021',
                    'year' => 2021,
                ],
                [
                    'id' => 2,
                    'title' => ['en' => 'Arrival'],
                    'original_title' => 'Arrival',
                    'imdb_id' => 'tt2543164',
                    'tmdb_id' => 329865,
                    'slug' => 'arrival-2016',
                    'year' => 2016,
                ],
            ],
            'meta' => [
                'query' => 'science fiction',
                'limit' => 10,
                'count' => 2,
            ],
        ], $response->getData(true));
    }

    public function test_handles_empty_movie_collection(): void
    {
        $response = MovieLookupResponse::fromCollection(collect(), 'indie', 5);

        $this->assertSame([
            'data' => [],
            'meta' => [
                'query' => 'indie',
                'limit' => 5,
                'count' => 0,
            ],
        ], $response->getData(true));
    }
}
