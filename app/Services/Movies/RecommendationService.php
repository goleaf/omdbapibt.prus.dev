<?php

namespace App\Services\Movies;

use App\Models\Movie;
use App\Models\User;
use App\Models\WatchHistory;
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
        $cacheKey = $this->cacheKey($user->getKey(), $limit);

        return $cache->remember($cacheKey, now()->addMinutes(45), function () use ($user, $limit) {
            $profile = $this->buildPreferenceProfile($user);

            $candidates = $this->candidateMovies($user, $limit * 4);

            return $candidates
                ->map(function (Movie $movie) use ($profile) {
                    return [
                        'movie' => $movie,
                        'score' => $this->scoreMovie($movie, $profile),
                    ];
                })
                ->sortByDesc('score')
                ->take($limit)
                ->values()
                ->map(fn (array $payload) => $payload['movie']);
        });
    }

    /**
     * Remove cached recommendations for the provided user.
     */
    public function flush(User $user): void
    {
        $cache = $this->cacheStore();

        foreach ([6, 12, 18, 24] as $limit) {
            $cache->forget($this->cacheKey($user->getKey(), $limit));
        }
    }

    /**
     * Build a lightweight preference profile from watch history data.
     *
     * @return array{
     *     genres: array<string, float>,
     *     average_rating: float,
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
        $now = now();

        foreach ($history as $entry) {
            $movie = $entry->watchable;

            if (! $movie instanceof Movie) {
                continue;
            }

            $recencyWeight = $this->recencyWeight($entry->watched_at ?? $entry->created_at, $now);

            foreach ($movie->genres as $genre) {
                $key = $genre->slug ?? $genre->name;
                $genreWeights[$key] = ($genreWeights[$key] ?? 0.0) + $recencyWeight;
            }

            if (! is_null($movie->vote_average)) {
                $ratingAccumulator += (float) $movie->vote_average * $recencyWeight;
                $ratingWeight += $recencyWeight;
            }

            if (! is_null($movie->year)) {
                $releaseAccumulator += (float) $movie->year * $recencyWeight;
                $releaseWeight += $recencyWeight;
            }
        }

        $normalizedGenres = $this->normalizeWeights($genreWeights);
        $averageRating = $ratingWeight > 0 ? $ratingAccumulator / $ratingWeight : 0.0;
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
        $ratingAlignment = $this->ratingAlignment($movie->vote_average, $profile['average_rating'] ?? null);
        $popularity = min(((float) ($movie->popularity ?? 0)) / 100, 1);

        $releaseAlignment = 0.0;
        if (! empty($profile['release_year']) && ! empty($movie->year)) {
            $difference = abs((float) $movie->year - (float) $profile['release_year']);
            $releaseAlignment = max(0, 1 - min($difference, 15) / 15);
        }

        return ($normalizedGenreScore * 0.45)
            + ($rating * 0.2)
            + ($ratingAlignment * 0.15)
            + ($popularity * 0.1)
            + ($releaseAlignment * 0.1);
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

        $query = Movie::query()
            ->with('genres')
            ->orderByDesc('popularity')
            ->take($limit);

        if ($watchedMovieIds->isNotEmpty()) {
            $query->whereNotIn('id', $watchedMovieIds);
        }

        return $query->get();
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

    protected function ratingAlignment(?float $movieRating, ?float $preferredRating): float
    {
        if (is_null($movieRating) || is_null($preferredRating)) {
            return 0.0;
        }

        $difference = abs($movieRating - $preferredRating);

        return max(0, 1 - min($difference, 4) / 4);
    }

    protected function recencyWeight(?Carbon $watchedAt, Carbon $now): float
    {
        if (is_null($watchedAt)) {
            return 1.0;
        }

        $days = max($watchedAt->diffInDays($now), 0);
        $weight = 1 / (1 + ($days / 14));

        return round(max($weight, 0.1), 4);
    }

    protected function cacheKey(int $userId, int $limit): string
    {
        return sprintf('recommendations:user:%d:%d', $userId, $limit);
    }

    protected function cacheStore(): CacheRepository
    {
        return $this->cacheManager->store(config('cache.default'));
    }
}
