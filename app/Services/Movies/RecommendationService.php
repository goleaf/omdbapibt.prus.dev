<?php

namespace App\Services\Movies;

use App\Models\Movie;
use App\Models\User;
use App\Models\WatchHistory;
use Illuminate\Cache\CacheManager;
use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
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
     *     release_year: float,
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
        $ratingCount = 0;
        $releaseAccumulator = 0.0;
        $releaseCount = 0;

        foreach ($history as $entry) {
            $movie = $entry->watchable;

            if (! $movie instanceof Movie) {
                continue;
            }

            foreach ($movie->genres as $genre) {
                $genreWeights[$genre->slug ?? $genre->name] = ($genreWeights[$genre->slug ?? $genre->name] ?? 0) + 1;
            }

            if (! is_null($movie->vote_average)) {
                $ratingAccumulator += (float) $movie->vote_average;
                $ratingCount++;
            }

            if (! is_null($movie->year)) {
                $releaseAccumulator += (float) $movie->year;
                $releaseCount++;
            }
        }

        $normalizedGenres = $this->normalizeWeights($genreWeights);
        $averageRating = $ratingCount > 0 ? $ratingAccumulator / $ratingCount : 0.0;
        $averageYear = $releaseCount > 0 ? $releaseAccumulator / $releaseCount : null;

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
        $popularity = min(((float) ($movie->popularity ?? 0)) / 100, 1);

        $releaseAlignment = 0.0;
        if (! empty($profile['release_year']) && ! empty($movie->year)) {
            $difference = abs((float) $movie->year - (float) $profile['release_year']);
            $releaseAlignment = max(0, 1 - min($difference, 15) / 15);
        }

        return ($normalizedGenreScore * 0.45)
            + ($rating * 0.3)
            + ($popularity * 0.15)
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

    protected function cacheStore(): CacheRepository
    {
        return $this->cacheManager->store(config('cache.default'));
    }
}
