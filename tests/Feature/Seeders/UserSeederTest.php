<?php

namespace Tests\Feature\Seeders;

use App\Models\User;
use App\Models\UserProfile;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_seeder_creates_users_with_profiles(): void
    {
        $this->seed(UserSeeder::class);

        // Seeder creates 1000 users total (including configured accounts)
        $this->assertSame(1000, User::query()->count());
        $this->assertSame(1000, UserProfile::query()->count());

        $profile = UserProfile::query()
            ->with([
                'user',
                'primaryGenre',
                'secondaryGenre',
                'favoriteMovie',
                'favoriteTvShow',
                'favoritePerson',
                'genrePreferences',
                'languagePreferences',
                'favoritePeople',
            ])
            ->first();
        $this->assertNotNull($profile);

        $this->assertNotNull($profile->bio);
        $this->assertNotNull($profile->primaryGenre);
        $this->assertNotNull($profile->secondaryGenre);
        $this->assertNotNull($profile->favoriteMovie);
        $this->assertNotNull($profile->favoritePerson);
        $this->assertNotNull($profile->home_country_id);
        $this->assertNotNull($profile->primary_language_id);
        $this->assertNotNull($profile->secondary_language_id);
        $this->assertNotNull($profile->subtitle_language_id);
        $this->assertGreaterThan(0, $profile->weekly_watch_minutes);
        $this->assertGreaterThan(0, $profile->average_session_minutes);
        $this->assertNotNull($profile->preferred_watch_hour);
        $this->assertGreaterThan(0, $profile->binge_watch_score);
        $this->assertGreaterThanOrEqual(0, $profile->rewatch_affinity);
        $this->assertNotNull($profile->last_watched_at);
        $this->assertIsArray($profile->recent_watch_highlights);
        $this->assertNotEmpty($profile->recent_watch_highlights);

        $highlight = $profile->recent_watch_highlights[0];
        $this->assertArrayHasKey('watch_history_id', $highlight);
        $this->assertDatabaseHas('watch_histories', [
            'id' => $highlight['watch_history_id'],
            'user_id' => $profile->user_id,
        ]);

        $this->assertGreaterThanOrEqual(1, $profile->genrePreferences->count());
        $this->assertGreaterThanOrEqual(1, $profile->languagePreferences->count());
        $this->assertGreaterThanOrEqual(1, $profile->favoritePeople->count());
    }
}
