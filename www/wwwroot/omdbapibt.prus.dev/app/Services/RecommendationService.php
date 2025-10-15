<?php

namespace App\Services;

use App\Models\Movie;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;

class RecommendationService
{
    /**
     * Fetch cached recommendations or calculate a fresh set for the given user.
     *
     * @return array{generated_at: string, items: array<int, array<string, mixed>>, profile: array<string, mixed>}
     */
    public function getRecommendationsFor(User $user, int $limit = 10): array
    {
        $cacheKey = $this->cacheKey($user, $limit);

        return Cache::remember($cacheKey, now()->addHours(6), function () use ($user, $limit) {
            $profile = $this->buildPreferenceProfile($user);
            $movies = $this->recommendationQuery($profile, $limit)->get();

            $items = $movies->map(function (Movie $movie) use ($profile): array {
                $score = (float) ($movie->recommendation_score ?? 0);

                return [
                    'id' => $movie->id,
                    'title' => $movie->title,
                    'year' => $movie->year,
                    'media_type' => $movie->media_type,
                    'popularity' => $movie->popularity,
                    'vote_average' => $movie->vote_average,
                    'poster_path' => $movie->poster_path,
                    'backdrop_path' => $movie->backdrop_path,
                    'score' => round($score, 2),
                    'reason' => $this->buildRationale($movie, $profile),
                ];
            })->values()->all();

            return [
                'generated_at' => now()->toIso8601String(),
                'items' => $items,
                'profile' => $profile,
            ];
        });
    }

    /**
     * Refresh the cached recommendations immediately.
     */
    public function refreshRecommendations(User $user, int $limit = 10): array
    {
        Cache::forget($this->cacheKey($user, $limit));

        return $this->getRecommendationsFor($user, $limit);
    }

    /**
     * Build a simple profile for the user based on watch history.
     *
     * @return array{watched_ids: array<int>, average_year: int|null, preferred_media_type: string|null, average_vote: float|null}
     */
    protected function buildPreferenceProfile(User $user): array
    {
        $histories = $user->watchHistories()
            ->with('movie:id,year,media_type,vote_average')
            ->get()
            ->filter(fn ($history) => $history->movie !== null);

        $watchedIds = $histories->pluck('movie_id')->all();

        if ($histories->isEmpty()) {
            return [
                'watched_ids' => $watchedIds,
                'average_year' => null,
                'preferred_media_type' => null,
                'average_vote' => null,
            ];
        }

        $years = $histories->pluck('movie.year')->filter();
        $averageYear = $years->isEmpty() ? null : (int) round($years->avg());

        $mediaType = $histories
            ->pluck('movie.media_type')
            ->filter()
            ->countBy()
            ->sortDesc()
            ->keys()
            ->first();

        $votes = $histories->pluck('movie.vote_average')->filter();
        $averageVote = $votes->isEmpty() ? null : round($votes->avg(), 1);

        return [
            'watched_ids' => $watchedIds,
            'average_year' => $averageYear,
            'preferred_media_type' => $mediaType,
            'average_vote' => $averageVote,
        ];
    }

    /**
     * Construct the query used to surface recommendations.
     *
     * @param  array{watched_ids: array<int>, average_year: int|null, preferred_media_type: string|null, average_vote: float|null}  $profile
     */
    protected function recommendationQuery(array $profile, int $limit): Builder
    {
        $watchedIds = Arr::get($profile, 'watched_ids', []);
        $preferredMediaType = Arr::get($profile, 'preferred_media_type');
        $averageYear = Arr::get($profile, 'average_year');
        $averageVote = Arr::get($profile, 'average_vote');

        $scoreFragments = [
            '(COALESCE(popularity, 0) * 0.6)',
            '(COALESCE(vote_average, 0) * 0.3)',
        ];

        $bindings = [];

        if (! empty($preferredMediaType)) {
            $scoreFragments[] = '(CASE WHEN media_type = ? THEN 15 ELSE 0 END)';
            $bindings[] = $preferredMediaType;
        }

        if (! empty($averageYear)) {
            $scoreFragments[] = '(CASE WHEN year IS NOT NULL THEN (20.0 / (1 + ABS(year - ?))) ELSE 0 END)';
            $bindings[] = $averageYear;
        }

        if (! empty($averageVote)) {
            $scoreFragments[] = '(CASE WHEN vote_average IS NOT NULL THEN LEAST(10, GREATEST(0, (10 - ABS(vote_average - ?)))) ELSE 0 END)';
            $bindings[] = $averageVote;
        }

        $scoreExpression = implode(' + ', $scoreFragments);

        return Movie::query()
            ->select('movies.*')
            ->selectRaw($scoreExpression.' as recommendation_score', $bindings)
            ->when(! empty($watchedIds), fn (Builder $query) => $query->whereNotIn('movies.id', $watchedIds))
            ->limit($limit)
            ->orderByDesc('recommendation_score')
            ->orderByDesc('popularity');
    }

    /**
     * Craft a rationale string for a suggested movie.
     *
     * @param  array{watched_ids: array<int>, average_year: int|null, preferred_media_type: string|null, average_vote: float|null}  $profile
     */
    protected function buildRationale(Movie $movie, array $profile): string
    {
        $parts = [];

        if (! empty($profile['preferred_media_type']) && $movie->media_type === $profile['preferred_media_type']) {
            $parts[] = 'Matches your preferred media type';
        }

        if (! empty($profile['average_year']) && ! empty($movie->year)) {
            $difference = abs($movie->year - $profile['average_year']);

            if ($difference === 0) {
                $parts[] = 'Released in the same year as titles you enjoyed';
            } elseif ($difference <= 5) {
                $parts[] = 'Released around the years you usually watch';
            }
        }

        if (! empty($movie->popularity)) {
            $parts[] = 'Currently popular with other viewers';
        }

        if (! empty($movie->vote_average)) {
            $parts[] = 'Critically rated at '.number_format((float) $movie->vote_average, 1).'/10';
        }

        if (empty($parts)) {
            $parts[] = 'Highly ranked across the catalogue';
        }

        return implode(' • ', $parts);
    }

    protected function cacheKey(User $user, int $limit): string
    {
        return sprintf('users:%d:recommendations:%d', $user->id, $limit);
    }
}
