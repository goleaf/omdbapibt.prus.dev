<?php

namespace Tests\Unit\Models;

use App\Models\Country;
use App\Models\Genre;
use App\Models\Language;
use App\Models\Movie;
use App\Models\Person;
use App\Models\Rating;
use App\Models\User;
use App\Models\WatchHistory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesLocaleTables;
use Tests\TestCase;

class MovieTest extends TestCase
{
    use CreatesLocaleTables;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->ensureLanguagesTable();
        $this->ensureCountriesTable();
    }

    public function test_title_cast_returns_localized_array(): void
    {
        $titles = [
            'en' => 'Interstellar',
            'es' => 'Interestelar',
        ];

        $movie = Movie::factory()->create([
            'title' => $titles,
        ]);

        $this->assertSame($titles, $movie->fresh()->title);
        $this->assertSame('Interstellar', $movie->fresh()->localizedTitle('en'));
        $this->assertSame('Interestelar', $movie->fresh()->localizedTitle('es'));
    }

    public function test_title_can_be_set_from_string(): void
    {
        $movie = Movie::factory()->create([
            'title' => 'Arrival',
        ]);

        $this->assertSame(['en' => 'Arrival'], $movie->fresh()->title);
    }

    public function test_scope_filters_by_vote_average(): void
    {
        Movie::factory()->create(['vote_average' => 4.5]);
        $matching = Movie::factory()->create(['vote_average' => 8.2]);

        $results = Movie::whereVoteAverageAtLeast(7.0)->pluck('id');

        $this->assertTrue($results->contains($matching->id));
        $this->assertCount(1, $results);
    }

    public function test_genres_languages_and_countries_relationships(): void
    {
        $movie = Movie::factory()->create();
        $genre = Genre::factory()->create();
        $language = Language::create([
            'name_translations' => ['en' => 'English', 'es' => 'Inglés'],
            'code' => 'en',
            'native_name_translations' => ['en' => 'English', 'es' => 'Inglés'],
            'active' => true,
        ]);
        $country = Country::create([
            'name_translations' => ['en' => 'United States', 'es' => 'Estados Unidos'],
            'code' => 'US',
            'active' => true,
        ]);

        $movie->genres()->attach($genre);
        $movie->languages()->attach($language);
        $movie->countries()->attach($country);

        $movie->refresh();

        $this->assertTrue($movie->genres->contains($genre));
        $this->assertTrue($movie->languages->contains($language));
        $this->assertTrue($movie->countries->contains($country));
    }

    public function test_people_relationship_includes_pivot_data(): void
    {
        $movie = Movie::factory()->create();
        $person = Person::factory()->create();

        $movie->people()->attach($person->id, [
            'credit_type' => 'crew',
            'department' => 'Directing',
            'character' => null,
            'job' => 'Director',
            'credit_order' => 1,
        ]);

        $movie->load('people');

        $pivot = $movie->people->first()->pivot;

        $this->assertSame('Director', $pivot->job);
        $this->assertSame('Directing', $pivot->department);
        $this->assertSame(1, $pivot->credit_order);
    }

    public function test_watchlist_and_watch_history_relationships(): void
    {
        $movie = Movie::factory()->create();
        $user = User::factory()->create();

        $movie->watchlistedBy()->attach($user);

        $history = WatchHistory::factory()
            ->forMovie($movie)
            ->create([
                'user_id' => $user->id,
            ]);

        $movie->load('watchlistedBy', 'watchHistories');

        $this->assertTrue($movie->watchlistedBy->contains($user));
        $this->assertTrue($movie->watchHistories->contains($history));
    }

    public function test_requires_subscription_reads_metadata_then_streaming_links(): void
    {
        $movie = Movie::factory()->create([
            'translation_metadata' => [
                'access' => [
                    'requires_subscription' => true,
                ],
            ],
            'streaming_links' => [
                'requires_subscription' => false,
            ],
        ]);

        $this->assertTrue($movie->requiresSubscription());

        $movie->translation_metadata = [];
        $movie->streaming_links = [
            'requires_subscription' => false,
        ];
        $movie->save();

        $this->assertFalse($movie->fresh()->requiresSubscription());
    }

    public function test_rating_helpers_return_user_state(): void
    {
        $movie = Movie::factory()->create();
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        Rating::factory()->create([
            'user_id' => $user->id,
            'movie_id' => $movie->id,
            'rating' => 9,
            'liked' => true,
            'disliked' => false,
        ]);

        $this->assertSame(9, $movie->userRating($user));
        $this->assertTrue($movie->likedBy($user));
        $this->assertFalse($movie->dislikedBy($user));

        $this->assertNull($movie->userRating($otherUser));
        $this->assertFalse($movie->likedBy($otherUser));
        $this->assertFalse($movie->dislikedBy($otherUser));
    }
}
