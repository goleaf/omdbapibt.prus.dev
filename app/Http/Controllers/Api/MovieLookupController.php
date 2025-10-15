<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MovieLookupController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'query' => ['required', 'string', 'min:2'],
            'limit' => ['sometimes', 'integer', 'min:1', 'max:50'],
        ]);

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

        return response()->json([
            'data' => $results,
        ]);
    }
}
