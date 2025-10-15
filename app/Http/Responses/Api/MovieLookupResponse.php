<?php

namespace App\Http\Responses\Api;

use App\Models\Movie;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;

class MovieLookupResponse
{
    /**
     * @param  Collection<int, Movie>  $movies
     */
    public static function fromCollection(Collection $movies, string $query, int $limit): JsonResponse
    {
        return response()->json([
            'data' => $movies
                ->map(static fn (Movie $movie): array => [
                    'id' => $movie->id,
                    'title' => $movie->title,
                    'original_title' => $movie->original_title,
                    'imdb_id' => $movie->imdb_id,
                    'tmdb_id' => $movie->tmdb_id,
                    'slug' => $movie->slug,
                    'year' => $movie->year,
                ])
                ->values(),
            'meta' => [
                'query' => $query,
                'limit' => $limit,
                'count' => $movies->count(),
            ],
        ]);
    }
}
