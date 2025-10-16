<?php

namespace Tests\Feature\Views;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfilePageTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_displays_all_profile_fields(): void
    {
        $user = User::factory()->create([
            'name' => 'Test User',
        ]);

        $profile = UserProfile::factory()
            ->for($user)
            ->create([
                'display_name' => 'CinemaBuff',
                'tagline' => 'Always exploring new worlds.',
                'bio' => 'Space opera fan and subtitled thriller devotee.',
                'location' => 'Portland, OR',
                'timezone' => 'America/Los_Angeles',
                'birthday' => '1990-07-16',
                'preferred_language' => 'English',
                'preferred_audio_language' => 'English Atmos',
                'preferred_subtitle_language' => 'Spanish',
                'content_maturity' => 'PG-13',
                'autoplay_next_episode' => true,
                'autoplay_trailers' => false,
                'newsletter_opt_in' => true,
                'marketing_opt_in' => false,
                'favorite_genre' => 'Science Fiction',
                'favorite_movie' => 'Arrival',
                'favorite_tv_show' => 'Andor',
                'favorite_actor' => 'Oscar Isaac',
                'favorite_director' => 'Denis Villeneuve',
                'favorite_quote' => 'The mystery is the force.',
                'website_url' => 'https://cinemabuff.dev',
                'twitter_url' => 'https://twitter.com/cinemabuff',
                'instagram_url' => 'https://instagram.com/cinemabuff',
                'tiktok_url' => 'https://tiktok.com/@cinemabuff',
                'youtube_url' => 'https://youtube.com/cinemabuff',
                'letterboxd_url' => 'https://letterboxd.com/cinemabuff',
                'discord_handle' => 'cinemabuff#2048',
            ]);

        $this->actingAs($user);

        $response = $this->get(route('account.profile', ['locale' => 'en']));

        $response->assertOk();

        $response->assertSeeTextInOrder([
            __('account.profile.sections.preferences.items.preferred_interface_language'),
            $profile->preferred_language,
            __('account.profile.sections.preferences.items.preferred_audio_language'),
            $profile->preferred_audio_language,
            __('account.profile.sections.preferences.items.preferred_subtitle_language'),
            $profile->preferred_subtitle_language,
            __('account.profile.sections.preferences.items.content_maturity'),
            $profile->content_maturity,
            __('account.profile.sections.preferences.items.autoplay_next_episode'),
            __('account.profile.values.enabled'),
            __('account.profile.sections.preferences.items.autoplay_trailers'),
            __('account.profile.values.disabled'),
            __('account.profile.sections.preferences.items.newsletter_opt_in'),
            __('account.profile.values.subscribed'),
            __('account.profile.sections.preferences.items.marketing_opt_in'),
            __('account.profile.values.opted_out'),
        ]);

        $response->assertSeeText($profile->favorite_genre);
        $response->assertSeeText($profile->favorite_movie);
        $response->assertSeeText($profile->favorite_tv_show);
        $response->assertSeeText($profile->favorite_actor);
        $response->assertSeeText($profile->favorite_director);
        $response->assertSeeText($profile->favorite_quote);

        $response->assertSeeText($profile->display_name);
        $response->assertSeeText($profile->tagline);
        $response->assertSeeText($profile->location);
        $response->assertSeeText($profile->timezone);
        $response->assertSeeText('July 16, 1990');
        $response->assertSeeText($profile->bio);
        $response->assertSeeText($profile->discord_handle);

        $response->assertSee($profile->website_url);
        $response->assertSee($profile->twitter_url);
        $response->assertSee($profile->instagram_url);
        $response->assertSee($profile->tiktok_url);
        $response->assertSee($profile->youtube_url);
        $response->assertSee($profile->letterboxd_url);
    }

    public function test_profile_page_uses_translations_for_non_default_locale(): void
    {
        $user = User::factory()->create([
            'name' => 'Localized User',
        ]);

        UserProfile::factory()
            ->for($user)
            ->create([
                'autoplay_next_episode' => true,
                'favorite_quote' => null,
            ]);

        $this->actingAs($user);

        $response = $this->get(route('account.profile', ['locale' => 'es']));

        $response->assertOk();

        $response->assertSeeText(__('account.profile.sections.preferences.title', [], 'es'));
        $response->assertSeeText(__('account.profile.sections.social.title', [], 'es'));
        $response->assertSeeText(__('account.profile.values.enabled', [], 'es'));
        $response->assertSeeText(__('account.profile.values.not_set', [], 'es'));
    }
}
