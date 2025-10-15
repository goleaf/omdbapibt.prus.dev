<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\MovieLookupRequest;
use App\Http\Responses\Api\MovieLookupResponse;
use App\Models\Movie;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;

class MovieLookupController extends Controller
{
    public function __invoke(MovieLookupRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $term = $validated['query'];
        $limit = $validated['limit'] ?? 10;

        $likeTerm = '%'.$term.'%';

        $results = Movie::query()
            ->where(function (Builder $builder) use ($term, $likeTerm): void {
                $builder
                    ->where('title', 'like', $likeTerm)
                    ->orWhere('original_title', 'like', $likeTerm)
                    ->orWhere('imdb_id', '=', $term)
                    ->orWhere('tmdb_id', '=', $term);
            })
            ->orderByDesc('updated_at')
            ->limit($limit)
            ->get([
                'id',
                'title',
                'original_title',
                'imdb_id',
                'tmdb_id',
                'slug',
                'year',
            ]);

        return MovieLookupResponse::fromCollection($results);
    }
}
