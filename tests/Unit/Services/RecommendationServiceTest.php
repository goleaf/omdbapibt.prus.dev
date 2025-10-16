<?php

namespace Tests\Unit\Services;

use App\Models\Country;
use App\Models\Genre;
use App\Models\Language;
use App\Models\Movie;
use App\Models\Person;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\WatchHistory;
use App\Services\Movies\RecommendationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class RecommendationServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config(['cache.default' => 'array']);
        Cache::store('array')->clear();
    }

    public function test_recommendations_exclude_watched_titles(): void
    {
        $service = app(RecommendationService::class);

        $user = User::factory()->create();
        $genre = Genre::factory()->create(['slug' => 'thriller']);
        $watched = Movie::factory()->create();
        $watched->genres()->attach($genre);
        $candidate = Movie::factory()->create([
            'vote_average' => 8.5,
            'popularity' => 320,
        ]);
        $candidate->genres()->attach($genre);

        WatchHistory::factory()->for($user)->forMovie($watched)->create();

        $results = $service->recommendFor($user);

        $this->assertTrue($results->contains(fn (Movie $movie) => $movie->id === $candidate->id));
        $this->assertFalse($results->contains(fn (Movie $movie) => $movie->id === $watched->id));
    }

    public function test_preference_profile_weights_recent_history_more_heavily(): void
    {
        $service = app(RecommendationService::class);

        $user = User::factory()->create();
        $recentGenre = Genre::factory()->create(['slug' => 'sci-fi']);
        $olderGenre = Genre::factory()->create(['slug' => 'drama']);

        $recentMovie = Movie::factory()->create([
            'vote_average' => 9.0,
            'year' => 2024,
        ]);
        $recentMovie->genres()->attach($recentGenre);

        $olderMovie = Movie::factory()->create([
            'vote_average' => 5.0,
            'year' => 1990,
        ]);
        $olderMovie->genres()->attach($olderGenre);

        WatchHistory::factory()->for($user)->forMovie($recentMovie)->create([
            'watched_at' => now()->subDays(3),
        ]);

        WatchHistory::factory()->for($user)->forMovie($olderMovie)->create([
            'watched_at' => now()->subDays(60),
        ]);

        $profile = $service->buildPreferenceProfile($user);

        $this->assertSame(1.0, $profile['genres'][$recentGenre->id]);
        $this->assertArrayHasKey($olderGenre->id, $profile['genres']);
        $this->assertLessThan(1.0, $profile['genres'][$olderGenre->id]);
        $this->assertGreaterThan(7.0, $profile['average_rating']);
        $this->assertNotNull($profile['release_year']);
        $this->assertGreaterThan($olderMovie->year, $profile['release_year']);
        $this->assertIsArray($profile['preferred_languages']);
        $this->assertIsArray($profile['favorite_people']);
    }

    public function test_recommendations_prioritize_explicit_profile_preferences(): void
    {
        $service = app(RecommendationService::class);

        $user = User::factory()->create();
        $preferredGenre = Genre::factory()->create();
        $secondaryGenre = Genre::factory()->create();
        $preferredLanguage = Language::factory()->create();
        $alternateLanguage = Language::factory()->create();
        $favoritePerson = Person::factory()->create();
        $alternatePerson = Person::factory()->create();
        $homeCountry = Country::factory()->create();

        $preferredMovie = Movie::factory()->create([
            'vote_average' => 8.9,
            'popularity' => 480,
        ]);
        $preferredMovie->genres()->sync([$preferredGenre->id]);
        $preferredMovie->languages()->sync([$preferredLanguage->id]);
        $preferredMovie->people()->sync([$favoritePerson->id]);
        $preferredMovie->countries()->sync([$homeCountry->id]);

        $alternateMovie = Movie::factory()->create([
            'vote_average' => 6.2,
            'popularity' => 310,
        ]);
        $alternateMovie->genres()->sync([$secondaryGenre->id]);
        $alternateMovie->languages()->sync([$alternateLanguage->id]);
        $alternateMovie->people()->sync([$alternatePerson->id]);
        $alternateMovie->countries()->sync([$homeCountry->id]);

        $profile = UserProfile::query()->create([
            'user_id' => $user->id,
            'bio' => 'Testing profile preferences',
            'home_country_id' => $homeCountry->id,
            'primary_genre_id' => $preferredGenre->id,
            'secondary_genre_id' => $secondaryGenre->id,
            'favorite_movie_id' => $preferredMovie->id,
            'favorite_tv_show_id' => null,
            'favorite_person_id' => $favoritePerson->id,
            'primary_language_id' => $preferredLanguage->id,
            'secondary_language_id' => $preferredLanguage->id,
            'subtitle_language_id' => $preferredLanguage->id,
            'weekly_watch_minutes' => 720,
            'average_session_minutes' => 120,
            'preferred_watch_hour' => 21,
            'binge_watch_score' => 0.92,
            'rewatch_affinity' => 0.58,
            'last_watched_at' => now(),
            'recent_watch_highlights' => [],
        ]);

        $profile->genrePreferences()->sync([
            $preferredGenre->id => ['preference_rank' => 1, 'preference_score' => 1.0],
            $secondaryGenre->id => ['preference_rank' => 2, 'preference_score' => 0.55],
        ]);

        $profile->languagePreferences()->sync([
            $preferredLanguage->id => ['preference_type' => 'audio', 'preference_rank' => 1],
        ]);

        $profile->favoritePeople()->sync([
            $favoritePerson->id => ['preference_rank' => 1, 'affinity_reason' => 'Top collaborator'],
        ]);

        $profile->touch();

        $recommendations = $service->recommendFor($user, 3);

        $this->assertSame($preferredMovie->id, $recommendations->first()->id);
        $this->assertTrue($recommendations->pluck('id')->contains($alternateMovie->id));

        $profileSnapshot = $service->buildPreferenceProfile($user);
        $this->assertArrayHasKey($preferredLanguage->id, $profileSnapshot['preferred_languages']);
        $this->assertSame(1.0, $profileSnapshot['preferred_languages'][$preferredLanguage->id]);
        $this->assertArrayHasKey($favoritePerson->id, $profileSnapshot['favorite_people']);
        $this->assertGreaterThan(0.7, $profileSnapshot['favorite_people'][$favoritePerson->id]);
    }
}
