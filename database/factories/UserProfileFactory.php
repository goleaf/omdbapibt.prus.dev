<?php

namespace Database\Factories;

use App\Models\Country;
use App\Models\Genre;
use App\Models\Language;
use App\Models\Movie;
use App\Models\Person;
use App\Models\TvShow;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\WatchHistory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * @extends Factory<UserProfile>
 */
class UserProfileFactory extends Factory
{
    protected $model = UserProfile::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $homeCountry = $this->randomModel(Country::class);
        $genres = $this->randomModels(Genre::class, 4);
        $languages = $this->randomModels(Language::class, 3);
        $people = $this->randomModels(Person::class, 3);
        $movies = $this->randomModels(Movie::class, 3);
        $tvShow = $this->randomModel(TvShow::class);

        $primaryGenre = Arr::get($genres, 0);
        $secondaryGenre = Arr::get($genres, 1, $primaryGenre);
        $primaryLanguage = Arr::get($languages, 0);
        $secondaryLanguage = Arr::get($languages, 1, $primaryLanguage);
        $subtitleLanguage = Arr::get($languages, 2, $primaryLanguage);

        $weeklyMinutes = $this->faker->numberBetween(360, 1_020);
        $sessionMinutes = $this->faker->numberBetween(45, 160);
        $bingeScore = round($this->faker->randomFloat(2, 0.45, 1.0), 2);
        $rewatchAffinity = round($this->faker->randomFloat(2, 0.2, 1.0), 2);
        $lastWatchedAt = Carbon::instance($this->faker->dateTimeBetween('-5 days', 'now'));

        return [
            'user_id' => User::factory(),
            // Social profile fields
            'display_name' => $this->faker->name(),
            'tagline' => $this->faker->sentence(6),
            'bio' => $this->faker->paragraphs(3, true),
            'location' => $this->faker->city(),
            'timezone' => $this->faker->timezone(),
            'birthday' => $this->faker->date(),
            // Preferences and settings
            'preferred_language' => 'English',
            'preferred_audio_language' => 'English (Dolby Atmos)',
            'preferred_subtitle_language' => 'English (CC)',
            'content_maturity' => 'PG-13',
            'autoplay_next_episode' => $this->faker->boolean(),
            'autoplay_trailers' => $this->faker->boolean(),
            'newsletter_opt_in' => $this->faker->boolean(),
            'marketing_opt_in' => $this->faker->boolean(),
            // String-based favorites
            'favorite_genre' => 'Science Fiction',
            'favorite_movie' => 'Interstellar',
            'favorite_tv_show' => 'The Expanse',
            'favorite_actor' => $this->faker->name(),
            'favorite_director' => $this->faker->name(),
            'favorite_quote' => 'Stay curious.',
            // Social media links
            'website_url' => $this->faker->url(),
            'twitter_url' => 'https://twitter.com/'.$this->faker->userName(),
            'instagram_url' => 'https://instagram.com/'.$this->faker->userName(),
            'tiktok_url' => 'https://tiktok.com/@'.$this->faker->userName(),
            'youtube_url' => 'https://youtube.com/'.$this->faker->userName(),
            'letterboxd_url' => 'https://letterboxd.com/'.$this->faker->userName(),
            'discord_handle' => $this->faker->userName().'#'.$this->faker->numberBetween(1000, 9999),
            // Relational favorites and preferences
            'home_country_id' => $homeCountry?->getKey(),
            'primary_genre_id' => $primaryGenre?->getKey(),
            'secondary_genre_id' => $secondaryGenre?->getKey(),
            'favorite_movie_id' => Arr::get($movies, 0)?->getKey(),
            'favorite_tv_show_id' => $tvShow?->getKey(),
            'favorite_person_id' => Arr::get($people, 0)?->getKey(),
            'primary_language_id' => $primaryLanguage?->getKey(),
            'secondary_language_id' => $secondaryLanguage?->getKey(),
            'subtitle_language_id' => $subtitleLanguage?->getKey(),
            // Viewer analytics
            'weekly_watch_minutes' => $weeklyMinutes,
            'average_session_minutes' => $sessionMinutes,
            'preferred_watch_hour' => $this->faker->numberBetween(10, 23),
            'binge_watch_score' => $bingeScore,
            'rewatch_affinity' => $rewatchAffinity,
            'last_watched_at' => $lastWatchedAt,
            'recent_watch_highlights' => [],
        ];
    }

    public function configure(): static
    {
        return $this
            ->afterCreating(function (UserProfile $profile): void {
                $this->seedGenrePreferences($profile);
                $this->seedLanguagePreferences($profile);
                $this->seedFavoritePeople($profile);
                $this->seedWatchHighlights($profile);
            });
    }

    /**
     * @template TModel of EloquentModel
     *
     * @param  class-string<TModel>  $model
     * @return TModel|null
     */
    protected function randomModel(string $model): ?EloquentModel
    {
        /** @var TModel|null $existing */
        $existing = $model::query()->inRandomOrder()->first();

        if ($existing) {
            return $existing;
        }

        return $model::factory()->create();
    }

    /**
     * @template TModel of EloquentModel
     *
     * @param  class-string<TModel>  $model
     * @return list<TModel>
     */
    protected function randomModels(string $model, int $count): array
    {
        /** @var Collection<int, TModel> $existing */
        $existing = $model::query()->inRandomOrder()->take($count)->get();

        if ($existing->count() < $count) {
            /** @var Collection<int, TModel> $additional */
            $additional = $model::factory()->count($count - $existing->count())->create();
            $existing = $existing->concat($additional);
        }

        return $existing->values()->all();
    }

    protected function seedGenrePreferences(UserProfile $profile): void
    {
        $genres = Genre::query()
            ->inRandomOrder()
            ->take(3)
            ->get();

        if ($genres->isEmpty()) {
            $genres = collect($this->randomModels(Genre::class, 3));
        }

        $rank = 1;
        foreach ($genres as $genre) {
            $profile->genrePreferences()->syncWithoutDetaching([
                $genre->getKey() => [
                    'preference_rank' => $rank,
                    'preference_score' => round(max(0.15, 1.05 - ($rank * 0.2)), 2),
                ],
            ]);
            $rank++;
        }
    }

    protected function seedLanguagePreferences(UserProfile $profile): void
    {
        $languageIds = array_filter([
            $profile->primary_language_id,
            $profile->secondary_language_id,
            $profile->subtitle_language_id,
        ]);

        $languages = Language::query()
            ->whereIn('id', $languageIds)
            ->get();

        $types = ['audio', 'secondary_audio', 'subtitle'];
        $rank = 1;

        foreach ($languages as $index => $language) {
            $profile->languagePreferences()->syncWithoutDetaching([
                $language->getKey() => [
                    'preference_type' => $types[$index] ?? 'interface',
                    'preference_rank' => $rank,
                ],
            ]);

            $rank++;
        }
    }

    protected function seedFavoritePeople(UserProfile $profile): void
    {
        $people = Person::query()
            ->inRandomOrder()
            ->take(3)
            ->get();

        if ($people->isEmpty()) {
            $people = collect($this->randomModels(Person::class, 3));
        }

        $rank = 1;
        foreach ($people as $person) {
            $profile->favoritePeople()->syncWithoutDetaching([
                $person->getKey() => [
                    'preference_rank' => $rank,
                    'affinity_reason' => $this->faker->randomElement([
                        'Memorable performance in recent watch',
                        'Consistently high-rated collaborations',
                        'Matches preferred storytelling style',
                    ]),
                ],
            ]);
            $rank++;
        }
    }

    protected function seedWatchHighlights(UserProfile $profile): void
    {
        $user = $profile->user;

        if (! $user instanceof User) {
            return;
        }

        $candidateMovies = Movie::query()
            ->with(['genres', 'people'])
            ->orderByDesc('popularity')
            ->take(6)
            ->get();

        if ($candidateMovies->isEmpty()) {
            $candidateMovies = collect($this->randomModels(Movie::class, 6));
        }

        $watchHistories = collect();
        $watchedAt = Carbon::now()->subDays(5);

        foreach ($candidateMovies as $movie) {
            $watchedAt = $watchedAt->addHours(random_int(6, 24));

            $watchHistory = WatchHistory::factory()
                ->for($user)
                ->forMovie($movie)
                ->create([
                    'watched_at' => $watchedAt,
                ]);

            $watchHistories->push($watchHistory);
        }

        $highlights = $watchHistories
            ->sortByDesc('watched_at')
            ->take(5)
            ->map(fn (WatchHistory $history) => [
                'watch_history_id' => $history->getKey(),
                'watchable_type' => $history->watchable_type,
                'watchable_id' => $history->watchable_id,
            ])
            ->values()
            ->all();

        $profile->forceFill([
            'recent_watch_highlights' => $highlights,
            'last_watched_at' => $watchHistories->max('watched_at'),
        ])->save();
    }
}
