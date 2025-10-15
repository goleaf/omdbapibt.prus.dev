<?php

namespace App\Services\Movies;

use App\Models\Movie;
use App\Models\User;
use App\Models\WatchHistory;
use Carbon\CarbonInterface;
use Illuminate\Cache\CacheManager;
use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class RecommendationService
{
    public function __construct(private CacheManager $cacheManager) {}

    /**
     * Retrieve recommended movies for the provided user, honoring cached results when available.
     */
    public function recommendFor(User $user, int $limit = 12): Collection
    {
        $cache = $this->cacheStore();
        $snapshot = $this->historySnapshot($user);
        $cacheKey = $this->cacheKey($user->getKey(), $limit);

        $cached = $cache->get($cacheKey);

        if (is_array($cached) && ($cached['version'] ?? null) === $snapshot['signature']) {
            return $this->hydrateMovies($cached['movie_ids'] ?? []);
        }

        $profile = $this->buildPreferenceProfile($user);

        $candidates = $this->candidateMovies($user, $limit * 4);

        $scored = $candidates
            ->map(function (Movie $movie) use ($profile) {
                return [
                    'movie' => $movie,
                    'score' => $this->scoreMovie($movie, $profile),
                ];
            })
            ->sortByDesc('score')
            ->take($limit)
            ->values();

        $movies = $scored->map(fn (array $payload) => $payload['movie']);

        $expiration = $this->cacheExpiration($snapshot['last_watched_at'], $snapshot['count']);

        $cache->put($cacheKey, [
            'version' => $snapshot['signature'],
            'movie_ids' => $movies->pluck('id')->all(),
        ], $expiration);

        $this->rememberCachedLimit($cache, $user->getKey(), $limit, $expiration);

        return $movies;
    }

    /**
     * Remove cached recommendations for the provided user.
     */
    public function flush(User $user): void
    {
        $cache = $this->cacheStore();

        $indexKey = $this->cacheIndexKey($user->getKey());

        $limits = collect($cache->get($indexKey, []))
            ->merge([6, 12, 18, 24])
            ->map(fn ($value) => (int) $value)
            ->unique()
            ->all();

        foreach ($limits as $limit) {
            $cache->forget($this->cacheKey($user->getKey(), $limit));
        }

        $cache->forget($indexKey);
    }

    /**
     * Retrieve the list of cached limits for the provided user.
     *
     * @return array<int, int>
     */
    public function cachedLimits(User $user): array
    {
        $cache = $this->cacheStore();

        return collect($cache->get($this->cacheIndexKey($user->getKey()), []))
            ->map(fn ($value) => (int) $value)
            ->unique()
            ->values()
            ->all();
    }

    /**
     * Build a lightweight preference profile from watch history data.
     *
     * @return array{
     *     genres: array<string, float>,
     *     average_rating: float|null,
     *     release_year: float|null,
     * }
     */
    public function buildPreferenceProfile(User $user): array
    {
        /** @var EloquentCollection<int, WatchHistory> $history */
        $history = WatchHistory::query()
            ->forUser($user)
            ->where('watchable_type', Movie::class)
            ->with(['watchable.genres'])
            ->latest('watched_at')
            ->take(100)
            ->get();

        $genreWeights = [];
        $ratingAccumulator = 0.0;
        $ratingWeight = 0.0;
        $releaseAccumulator = 0.0;
        $releaseWeight = 0.0;
        $now = Carbon::now();

        foreach ($history as $entry) {
            $movie = $entry->watchable;

            if (! $movie instanceof Movie) {
                continue;
            }

            $weight = $this->recencyWeight($entry->watched_at ?? $entry->updated_at ?? null, $now);

            foreach ($movie->genres as $genre) {
                $key = $genre->slug ?? $genre->name;
                $genreWeights[$key] = ($genreWeights[$key] ?? 0) + $weight;
            }

            if (! is_null($movie->vote_average)) {
                $ratingAccumulator += $weight * (float) $movie->vote_average;
                $ratingWeight += $weight;
            }

            $releaseYear = $movie->year;

            if (is_null($releaseYear) && $movie->release_date) {
                $releaseYear = (int) $movie->release_date->format('Y');
            }

            if (! is_null($releaseYear)) {
                $releaseAccumulator += $weight * (float) $releaseYear;
                $releaseWeight += $weight;
            }
        }

        $normalizedGenres = $this->normalizeWeights($genreWeights);
        $averageRating = $ratingWeight > 0 ? $ratingAccumulator / $ratingWeight : null;
        $averageYear = $releaseWeight > 0 ? $releaseAccumulator / $releaseWeight : null;

        return [
            'genres' => $normalizedGenres,
            'average_rating' => $averageRating,
            'release_year' => $averageYear,
        ];
    }

    /**
     * Calculate the recommendation score for the provided movie.
     */
    protected function scoreMovie(Movie $movie, array $profile): float
    {
        $genreScore = 0.0;
        $genreWeight = 0.0;

        foreach ($movie->genres as $genre) {
            $key = $genre->slug ?? $genre->name;
            $genreScore += $profile['genres'][$key] ?? 0.0;
            $genreWeight += 1;
        }

        $normalizedGenreScore = $genreWeight > 0 ? $genreScore / $genreWeight : 0.0;

        $rating = (float) ($movie->vote_average ?? 0) / 10;
        $popularity = min(((float) ($movie->popularity ?? 0)) / 400, 1);

        $ratingAlignment = 0.0;

        if (! is_null($profile['average_rating']) && ! is_null($movie->vote_average)) {
            $difference = abs((float) $movie->vote_average - (float) $profile['average_rating']);
            $ratingAlignment = max(0.0, 1 - min($difference, 5) / 5);
        }

        $releaseAlignment = 0.0;
        $releaseYear = $movie->year;

        if (is_null($releaseYear) && $movie->release_date) {
            $releaseYear = (int) $movie->release_date->format('Y');
        }

        if (! empty($profile['release_year']) && ! empty($releaseYear)) {
            $difference = abs((float) $releaseYear - (float) $profile['release_year']);
            $releaseAlignment = max(0.0, 1 - min($difference, 20) / 20);
        }

        return ($normalizedGenreScore * 0.4)
            + ($rating * 0.2)
            + ($ratingAlignment * 0.25)
            + ($popularity * 0.1)
            + ($releaseAlignment * 0.05);
    }

    /**
     * Retrieve candidate movies for scoring.
     *
     * @return Collection<int, Movie>
     */
    protected function candidateMovies(User $user, int $limit): Collection
    {
        $watchedMovieIds = WatchHistory::query()
            ->forUser($user)
            ->where('watchable_type', Movie::class)
            ->pluck('watchable_id');

        return Movie::query()
            ->with('genres')
            ->whereNotIn('id', $watchedMovieIds)
            ->orderByDesc('popularity')
            ->take($limit)
            ->get();
    }

    /**
     * Normalize the provided weights so that the highest weight equals 1.
     *
     * @param  array<string, float|int>  $weights
     * @return array<string, float>
     */
    protected function normalizeWeights(array $weights): array
    {
        if ($weights === []) {
            return [];
        }

        $max = max($weights);

        if ($max <= 0) {
            return [];
        }

        return collect($weights)
            ->map(fn ($value) => round(((float) $value) / $max, 4))
            ->all();
    }

    protected function cacheKey(int $userId, int $limit): string
    {
        return sprintf('recommendations:user:%d:%d', $userId, $limit);
    }

    protected function cacheIndexKey(int $userId): string
    {
        return sprintf('recommendations:user:%d:index', $userId);
    }

    protected function cacheStore(): CacheRepository
    {
        return $this->cacheManager->store(config('cache.default'));
    }

    /**
     * Compute a signature for the user's watch history to drive cache invalidation.
     *
     * @return array{signature:string,count:int,last_watched_at:?Carbon}
     */
    protected function historySnapshot(User $user): array
    {
        $result = WatchHistory::query()
            ->forUser($user)
            ->selectRaw('COUNT(*) as aggregate_count, MAX(updated_at) as last_updated_at, MAX(watched_at) as last_watched_at')
            ->first();

        $count = (int) ($result?->aggregate_count ?? 0);
        $lastUpdatedAt = $result?->last_updated_at ? Carbon::parse($result->last_updated_at) : null;
        $lastWatchedAt = $result?->last_watched_at ? Carbon::parse($result->last_watched_at) : null;

        return [
            'signature' => sprintf('%d:%d', $count, $lastUpdatedAt?->getTimestamp() ?? 0),
            'count' => $count,
            'last_watched_at' => $lastWatchedAt,
        ];
    }

    protected function cacheExpiration(?Carbon $lastWatchedAt, int $historyCount): Carbon
    {
        $now = Carbon::now();
        $minutes = 60;

        if ($historyCount === 0) {
            $minutes = 15;
        } elseif ($lastWatchedAt && $lastWatchedAt->greaterThan((clone $now)->subDay())) {
            $minutes = 20;
        } elseif ($lastWatchedAt && $lastWatchedAt->greaterThan((clone $now)->subWeek())) {
            $minutes = 40;
        }

        return $now->addMinutes($minutes);
    }

    /**
     * Convert cached movie identifiers back into hydrated models.
     *
     * @param  array<int, int>  $ids
     * @return Collection<int, Movie>
     */
    protected function hydrateMovies(array $ids): Collection
    {
        if ($ids === []) {
            return collect();
        }

        $movies = Movie::query()
            ->with('genres')
            ->whereIn('id', $ids)
            ->get()
            ->keyBy('id');

        return collect($ids)
            ->map(fn (int $id) => $movies->get($id))
            ->filter()
            ->values();
    }

    protected function recencyWeight(?CarbonInterface $watchedAt, CarbonInterface $reference): float
    {
        if (! $watchedAt) {
            return 1.0;
        }

        $days = max($watchedAt->diffInDays($reference), 0);

        return round(1 / (1 + ($days / 14)), 4);
    }

    protected function rememberCachedLimit(CacheRepository $cache, int $userId, int $limit, Carbon $expiresAt): void
    {
        $indexKey = $this->cacheIndexKey($userId);

        $limits = collect($cache->get($indexKey, []))
            ->map(fn ($value) => (int) $value)
            ->push($limit)
            ->unique()
            ->values()
            ->all();

        $cache->put($indexKey, $limits, $expiresAt);
    }
}
