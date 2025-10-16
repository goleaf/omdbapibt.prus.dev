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
        $snapshot = $this->historySnapshot($user);
        $profile = $this->buildPreferenceProfile($user);
        $profileVersion = (string) ($profile['version'] ?? 0);
        $preferenceSignature = sprintf('%s:%s', $snapshot['signature'], $profileVersion);
        $cacheKey = $this->cacheKey($user->getKey(), $limit);

        $cached = $cache->get($cacheKey);

        if (is_array($cached) && ($cached['version'] ?? null) === $preferenceSignature) {
            return $this->hydrateMovies($cached['movie_ids'] ?? []);
        }

        unset($profile['version']);

        $candidates = $this->candidateMovies($user, $limit * 6, $profile);

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
            'version' => $preferenceSignature,
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
     *     genres: array<int, float>,
     *     average_rating: float,
     *     release_year: float|null,
     *     preferred_languages: array<int, float>,
     *     favorite_people: array<int, float>,
     *     home_country_id: int|null,
     *     favorite_movie_vector: array<string, array<int, int>>|null,
     *     version: int,
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
        $preferredLanguages = [];
        $favoritePeople = [];
        $favoriteMovieVector = null;
        $homeCountryId = null;
        $profileVersion = 0;
        $now = now();

        foreach ($history as $entry) {
            $movie = $entry->watchable;

            if (! $movie instanceof Movie) {
                continue;
            }

            $recencyWeight = $this->recencyWeight($entry->watched_at ?? $entry->created_at, $now);

            foreach ($movie->genres as $genre) {
                $key = $genre->getKey();
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

        $profileModel = $user->profile()
            ->with([
                'primaryGenre',
                'secondaryGenre',
                'genrePreferences',
                'languagePreferences',
                'favoritePeople',
                'favoriteMovie.genres',
                'favoriteMovie.languages',
                'favoriteMovie.people',
                'favoriteMovie.countries',
                'favoritePerson',
            ])
            ->first();

        if ($profileModel) {
            $profileVersion = $profileModel->updated_at?->getTimestamp() ?? 0;
            $homeCountryId = $profileModel->home_country_id;

            if ($profileModel->primaryGenre) {
                $key = $profileModel->primaryGenre->getKey();
                $genreWeights[$key] = ($genreWeights[$key] ?? 0.0) + 1.2;
            }

            if ($profileModel->secondaryGenre) {
                $key = $profileModel->secondaryGenre->getKey();
                $genreWeights[$key] = ($genreWeights[$key] ?? 0.0) + 0.8;
            }

            foreach ($profileModel->genrePreferences as $genre) {
                $key = $genre->getKey();
                $genreWeights[$key] = ($genreWeights[$key] ?? 0.0) + (float) $genre->pivot->preference_score;

                if ($genre->pivot?->updated_at) {
                    $profileVersion = max($profileVersion, Carbon::parse($genre->pivot->updated_at)->getTimestamp());
                }
            }

            $languageWeightHints = [
                $profileModel->primary_language_id => 1.0,
                $profileModel->secondary_language_id => 0.85,
                $profileModel->subtitle_language_id => 0.65,
            ];

            foreach ($languageWeightHints as $languageId => $weight) {
                if ($languageId) {
                    $preferredLanguages[$languageId] = max($preferredLanguages[$languageId] ?? 0.0, $weight);
                }
            }

            foreach ($profileModel->languagePreferences as $language) {
                $rankModifier = max(1, (int) $language->pivot->preference_rank);
                $typeWeight = match ($language->pivot->preference_type) {
                    'audio' => 1.0,
                    'secondary_audio' => 0.85,
                    'subtitle' => 0.65,
                    default => 0.55,
                };

                $weight = max(0.2, $typeWeight - (($rankModifier - 1) * 0.05));
                $preferredLanguages[$language->getKey()] = max($preferredLanguages[$language->getKey()] ?? 0.0, $weight);

                if ($language->pivot?->updated_at) {
                    $profileVersion = max($profileVersion, Carbon::parse($language->pivot->updated_at)->getTimestamp());
                }
            }

            foreach ($profileModel->favoritePeople as $person) {
                $rankModifier = max(1, (int) $person->pivot->preference_rank);
                $weight = max(0.25, 1.1 - (($rankModifier - 1) * 0.25));
                $favoritePeople[$person->getKey()] = max($favoritePeople[$person->getKey()] ?? 0.0, $weight);

                if ($person->pivot?->updated_at) {
                    $profileVersion = max($profileVersion, Carbon::parse($person->pivot->updated_at)->getTimestamp());
                }
            }

            if ($profileModel->favorite_person_id) {
                $favoritePeople[$profileModel->favorite_person_id] = max($favoritePeople[$profileModel->favorite_person_id] ?? 0.0, 1.0);
            }

            $favoriteMovie = $profileModel->favoriteMovie;

            if ($favoriteMovie instanceof Movie) {
                $favoriteMovieVector = [
                    'genres' => $favoriteMovie->genres->pluck('id')->all(),
                    'languages' => $favoriteMovie->languages->pluck('id')->all(),
                    'people' => $favoriteMovie->people->pluck('id')->all(),
                    'countries' => $favoriteMovie->countries->pluck('id')->all(),
                ];

                foreach ($favoriteMovie->genres as $genre) {
                    $key = $genre->getKey();
                    $genreWeights[$key] = ($genreWeights[$key] ?? 0.0) + 0.4;
                }
            }
        }

        $normalizedGenres = $this->normalizeWeights($genreWeights);
        $normalizedLanguages = $this->normalizeWeights($preferredLanguages);
        $normalizedPeople = $this->normalizeWeights($favoritePeople);
        $averageRating = $ratingWeight > 0 ? $ratingAccumulator / $ratingWeight : 0.0;
        $averageYear = $releaseWeight > 0 ? $releaseAccumulator / $releaseWeight : null;

        return [
            'genres' => $normalizedGenres,
            'average_rating' => $averageRating,
            'release_year' => $averageYear,
            'preferred_languages' => $normalizedLanguages,
            'favorite_people' => $normalizedPeople,
            'home_country_id' => $homeCountryId,
            'favorite_movie_vector' => $favoriteMovieVector,
            'version' => $profileVersion,
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
            $key = $genre->getKey();
            $genreScore += $profile['genres'][$key] ?? 0.0;
            $genreWeight += 1;
        }

        $normalizedGenreScore = $genreWeight > 0 ? $genreScore / $genreWeight : 0.0;

        $rating = (float) ($movie->vote_average ?? 0) / 10;
        $popularity = min(((float) ($movie->popularity ?? 0)) / 100, 1);
        $ratingAlignment = $this->ratingAlignment($movie->vote_average, $profile['average_rating'] ?? null);
        $releaseAlignment = $this->releaseAlignment($movie->year, $profile['release_year'] ?? null);
        $languageAlignment = $this->languageAlignment($movie, $profile['preferred_languages'] ?? []);
        $personAffinity = $this->personAffinity($movie, $profile['favorite_people'] ?? []);
        $countryAffinity = $this->countryAffinity($movie, $profile['home_country_id'] ?? null);
        $favoriteSimilarity = $this->favoriteMovieSimilarity($movie, $profile['favorite_movie_vector'] ?? null);

        return ($normalizedGenreScore * 0.32)
            + ($languageAlignment * 0.18)
            + ($rating * 0.12)
            + ($ratingAlignment * 0.12)
            + ($popularity * 0.08)
            + ($releaseAlignment * 0.06)
            + ($personAffinity * 0.07)
            + ($countryAffinity * 0.025)
            + ($favoriteSimilarity * 0.085);
    }

    /**
     * Retrieve candidate movies for scoring.
     *
     * @return Collection<int, Movie>
     */
    protected function candidateMovies(User $user, int $limit, array $profile): Collection
    {
        $watchedMovieIds = WatchHistory::query()
            ->forUser($user)
            ->where('watchable_type', Movie::class)
            ->pluck('watchable_id');

        $preferredLanguageIds = array_keys($profile['preferred_languages'] ?? []);

        $baseQuery = Movie::query()
            ->with(['genres', 'languages', 'people', 'countries'])
            ->orderByDesc('popularity');

        if ($watchedMovieIds->isNotEmpty()) {
            $baseQuery->whereNotIn('id', $watchedMovieIds);
        }

        $languageFiltered = clone $baseQuery;

        if ($preferredLanguageIds !== []) {
            $languageFiltered->whereHas('languages', function ($query) use ($preferredLanguageIds): void {
                $query->whereIn('languages.id', $preferredLanguageIds);
            });
        }

        /** @var Collection<int, Movie> $primary */
        $primary = $languageFiltered->take($limit)->get();

        if ($primary->count() >= $limit) {
            return $primary->unique('id')->take($limit);
        }

        $alreadySelected = $primary->pluck('id');

        if ($alreadySelected->isNotEmpty()) {
            $baseQuery->whereNotIn('id', $alreadySelected);
        }

        $fallback = $baseQuery
            ->take($limit - $primary->count())
            ->get();

        return $primary->concat($fallback)->unique('id');
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

    protected function releaseAlignment(?int $movieYear, ?float $preferredYear): float
    {
        if (is_null($movieYear) || is_null($preferredYear)) {
            return 0.0;
        }

        $difference = abs((float) $movieYear - $preferredYear);

        return max(0.0, 1 - min($difference, 20) / 20);
    }

    /**
     * @param  array<int, float>  $preferredLanguages
     */
    protected function languageAlignment(Movie $movie, array $preferredLanguages): float
    {
        if ($preferredLanguages === []) {
            return 0.0;
        }

        $movieLanguageIds = $movie->languages->pluck('id')->all();

        if ($movieLanguageIds === []) {
            return 0.0;
        }

        $maxPreference = max($preferredLanguages);

        if ($maxPreference <= 0) {
            return 0.0;
        }

        $score = 0.0;
        $matched = 0;

        foreach ($movieLanguageIds as $languageId) {
            if (! array_key_exists($languageId, $preferredLanguages)) {
                continue;
            }

            $score += $preferredLanguages[$languageId];
            $matched++;
        }

        if ($matched === 0) {
            return 0.0;
        }

        return min(1.0, ($score / $matched) / $maxPreference);
    }

    /**
     * @param  array<int, float>  $favoritePeople
     */
    protected function personAffinity(Movie $movie, array $favoritePeople): float
    {
        if ($favoritePeople === []) {
            return 0.0;
        }

        $moviePersonIds = $movie->people->pluck('id')->all();

        if ($moviePersonIds === []) {
            return 0.0;
        }

        $totalPreference = array_sum($favoritePeople);

        if ($totalPreference <= 0) {
            return 0.0;
        }

        $score = 0.0;

        foreach ($moviePersonIds as $personId) {
            $score += $favoritePeople[$personId] ?? 0.0;
        }

        return min(1.0, $score / $totalPreference);
    }

    protected function countryAffinity(Movie $movie, ?int $countryId): float
    {
        if (! $countryId) {
            return 0.0;
        }

        return $movie->countries->contains('id', $countryId) ? 1.0 : 0.0;
    }

    /**
     * @param  array<string, array<int, int>>|null  $favoriteVector
     */
    protected function favoriteMovieSimilarity(Movie $movie, ?array $favoriteVector): float
    {
        if (! is_array($favoriteVector)) {
            return 0.0;
        }

        $genreScore = $this->overlapScore($movie->genres->pluck('id')->all(), $favoriteVector['genres'] ?? []);
        $languageScore = $this->overlapScore($movie->languages->pluck('id')->all(), $favoriteVector['languages'] ?? []);
        $peopleScore = $this->overlapScore($movie->people->pluck('id')->all(), $favoriteVector['people'] ?? []);
        $countryScore = $this->overlapScore($movie->countries->pluck('id')->all(), $favoriteVector['countries'] ?? []);

        return ($genreScore * 0.5)
            + ($languageScore * 0.2)
            + ($peopleScore * 0.2)
            + ($countryScore * 0.1);
    }

    /**
     * @param  array<int, int>  $left
     * @param  array<int, int>  $right
     */
    protected function overlapScore(array $left, array $right): float
    {
        $left = array_values(array_unique(array_map('intval', $left)));
        $right = array_values(array_unique(array_map('intval', $right)));

        if ($left === [] || $right === []) {
            return 0.0;
        }

        $intersection = count(array_intersect($left, $right));
        $union = count(array_unique(array_merge($left, $right)));

        if ($union === 0) {
            return 0.0;
        }

        return $intersection / $union;
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
            ->with(['genres', 'languages', 'people', 'countries'])
            ->whereIn('id', $ids)
            ->get()
            ->keyBy('id');

        return collect($ids)
            ->map(fn (int $id) => $movies->get($id))
            ->filter()
            ->values();
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
