<?php

namespace App\Services\Movies;

use App\Models\Movie;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class ParsedMoviePersister
{
    public function __construct(protected MovieCacheService $movieCache) {}

    /**
     * Persist parsed movie attributes while intentionally bypassing cache writes.
     *
     * The persistence layer keeps the database as the single source of truth to avoid
     * storing partially parsed payloads. Downstream caches are invalidated so they can
     * be rebuilt from the committed records instead of stale, parsed snapshots.
     */
    public function persist(array $attributes): Movie
    {
        $identifierKey = collect(['tmdb_id', 'imdb_id'])->first(function (string $key) use ($attributes) {
            return ! empty($attributes[$key]);
        });

        if (! $identifierKey) {
            throw new InvalidArgumentException('A tmdb_id or imdb_id attribute is required to persist a movie.');
        }

        $identifier = [$identifierKey => $attributes[$identifierKey]];
        $payload = Arr::except($attributes, [$identifierKey]);

        $movie = DB::transaction(function () use ($identifier, $payload) {
            return Movie::query()->updateOrCreate($identifier, $payload);
        });

        $this->movieCache->invalidateTrending();
        $this->movieCache->invalidatePopular();

        return $movie;
    }
}
