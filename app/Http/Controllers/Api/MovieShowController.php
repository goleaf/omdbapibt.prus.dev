<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use Illuminate\Http\JsonResponse;

class MovieShowController extends Controller
{
    public function __invoke(Movie $movie): JsonResponse
    {
        return response()->json([
            'data' => [
                'id' => $movie->getKey(),
                'slug' => $movie->slug,
                'title' => $movie->title,
                'original_title' => $movie->original_title,
                'year' => $movie->year,
                'popularity' => $movie->popularity,
                'vote_average' => $movie->vote_average,
                'vote_count' => $movie->vote_count,
            ],
        ]);
    }
}
