<?php

namespace App\Services\Movies;

use App\Models\Movie;
use App\Models\User;
use App\Models\WatchHistory;
use Illuminate\Cache\CacheManager;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection as SupportCollection;

class RecommendationService
{
    public function __construct(protected CacheManager $cache) {}

    /**
     * Retrieve cached recommendations for the given user.
     */
    public function forUser(User $user, int $limit = 12): Collection
    {
        $tags = $this->cacheTags($user);
        $key = $this->cacheKey($user, $limit);

        return $this->cache
            ->tags($tags)
            ->remember(
                $key,
                Carbon::now()->addSeconds((int) config('cache_ttls.queries.recommendations', 60 * 60)),
                fn () => $this->generateRecommendations($user, $limit)
            );
    }

    /**
     * Rebuild the cached recommendations for the user.
     */
    public function refresh(User $user, int $limit = 12): Collection
    {
        $this->flush($user);

        return $this->forUser($user, $limit);
    }

    /**
     * Flush all recommendation caches for the user.
     */
    public function flush(User $user): void
    {
        $this->cache->tags($this->cacheTags($user))->flush();
    }

    /**
     * Generate personalized recommendations for the user.
     */
    protected function generateRecommendations(User $user, int $limit): Collection
    {
        $profile = $this->buildPreferenceProfile($user);

        $recommendations = $this->scoreUnseenMovies($user, $profile, $limit * 5)
            ->take($limit)
            ->values();

        if ($recommendations->isEmpty()) {
            return $this->fallbackRecommendations($limit);
        }

        return $recommendations;
    }

    /**
     * Build the preference profile for the user based on watch history.
     */
    protected function buildPreferenceProfile(User $user): SupportCollection
    {
        $histories = $user->watchHistories()
            ->with(['movie.genres'])
            ->get();

        if ($histories->isEmpty()) {
            return collect();
        }

        $genreWeights = collect();

        $histories->each(function (WatchHistory $history) use (&$genreWeights): void {
            $weight = max(1, (int) $history->rewatch_count);

            $history->movie->genres->each(function ($genre) use (&$genreWeights, $weight): void {
                $key = $genre->getKey();
                $genreWeights[$key] = ($genreWeights[$key] ?? 0) + $weight;
            });
        });

        $totalWeight = (float) $genreWeights->sum();

        if ($totalWeight === 0.0) {
            return collect();
        }

        return $genreWeights->map(fn (float $weight): float => $weight / $totalWeight);
    }

    /**
     * Score movies the user has not watched against their preference profile.
     */
    protected function scoreUnseenMovies(User $user, SupportCollection $profile, int $candidateLimit): Collection
    {
        $watchedMovieIds = $user->watchHistories()->pluck('movie_id');

        $candidates = Movie::query()
            ->with('genres')
            ->when($watchedMovieIds->isNotEmpty(), fn ($query) => $query->whereNotIn('id', $watchedMovieIds))
            ->orderByDesc('vote_average')
            ->orderByDesc('popularity')
            ->limit(max($candidateLimit, 25))
            ->get();

        return $candidates
            ->map(function (Movie $movie) use ($profile): Movie {
                $preferenceScore = $movie->genres->sum(fn ($genre) => $profile->get($genre->getKey(), 0.0));
                $ratingScore = $movie->vote_average ? ($movie->vote_average / 10) : 0.0;
                $popularityScore = $this->normalizePopularity((float) $movie->popularity);

                $score = ($preferenceScore * 0.6) + ($ratingScore * 0.3) + ($popularityScore * 0.1);

                $movie->setAttribute('recommendation_score', round($score, 4));

                return $movie;
            })
            ->sortByDesc(fn (Movie $movie) => $movie->getAttribute('recommendation_score'))
            ->values();
    }

    /**
     * Provide fallback recommendations when no personalized data exists.
     */
    protected function fallbackRecommendations(int $limit): Collection
    {
        return Movie::query()
            ->with('genres')
            ->orderByDesc('popularity')
            ->orderByDesc('vote_average')
            ->limit($limit)
            ->get()
            ->each(function (Movie $movie): void {
                $ratingScore = $movie->vote_average ? ($movie->vote_average / 10) : 0.0;
                $popularityScore = $this->normalizePopularity((float) $movie->popularity);
                $movie->setAttribute('recommendation_score', round(($ratingScore * 0.7) + ($popularityScore * 0.3), 4));
            });
    }

    /**
     * Normalize popularity into a 0-1 range using a log scale.
     */
    protected function normalizePopularity(float $popularity): float
    {
        if ($popularity <= 0) {
            return 0.0;
        }

        return min(1.0, log($popularity + 1, 10));
    }

    /**
     * Get cache tags for the user.
     *
     * @return array<int, string>
     */
    protected function cacheTags(User $user): array
    {
        return ['movies', 'recommendations', 'user:'.$user->getKey()];
    }

    protected function cacheKey(User $user, int $limit): string
    {
        return sprintf('movies.recommendations.%d.%d', $user->getKey(), $limit);
    }
}
