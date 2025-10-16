<?php

namespace Database\Factories;

use App\Models\UserProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UserProfile>
 */
class UserProfileFactory extends Factory
{
    protected $model = UserProfile::class;

    public function definition(): array
    {
        return [
            'display_name' => $this->faker->name(),
            'tagline' => $this->faker->sentence(6),
            'bio' => $this->faker->paragraph(),
            'location' => $this->faker->city(),
            'timezone' => $this->faker->timezone(),
            'birthday' => $this->faker->date(),
            'preferred_language' => 'English',
            'preferred_audio_language' => 'English (Dolby Atmos)',
            'preferred_subtitle_language' => 'English (CC)',
            'content_maturity' => 'PG-13',
            'autoplay_next_episode' => $this->faker->boolean(),
            'autoplay_trailers' => $this->faker->boolean(),
            'newsletter_opt_in' => $this->faker->boolean(),
            'marketing_opt_in' => $this->faker->boolean(),
            'favorite_genre' => 'Science Fiction',
            'favorite_movie' => 'Interstellar',
            'favorite_tv_show' => 'The Expanse',
            'favorite_actor' => $this->faker->name(),
            'favorite_director' => $this->faker->name(),
            'favorite_quote' => 'Stay curious.',
            'website_url' => $this->faker->url(),
            'twitter_url' => 'https://twitter.com/'.$this->faker->userName(),
            'instagram_url' => 'https://instagram.com/'.$this->faker->userName(),
            'tiktok_url' => 'https://tiktok.com/@'.$this->faker->userName(),
            'youtube_url' => 'https://youtube.com/'.$this->faker->userName(),
            'letterboxd_url' => 'https://letterboxd.com/'.$this->faker->userName(),
            'discord_handle' => $this->faker->userName().'#'.$this->faker->numberBetween(1000, 9999),
        ];
    }
}
