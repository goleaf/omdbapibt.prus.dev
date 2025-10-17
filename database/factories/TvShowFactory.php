<?php

namespace Database\Factories;

use App\Models\TvShow;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<TvShow>
 */
class TvShowFactory extends Factory
{
    protected $model = TvShow::class;

    public function definition(): array
    {
        $nameEn = $this->faker->unique()->sentence(3);
        $nameTranslations = [
            'en' => $nameEn,
            'es' => $this->faker->sentence(3).' (ES)',
            'fr' => $this->faker->sentence(3).' (FR)',
        ];
        $overviewTranslations = [
            'en' => $this->faker->paragraph(),
            'es' => $this->faker->paragraph().' (ES)',
            'fr' => $this->faker->paragraph().' (FR)',
        ];
        $taglineTranslations = [
            'en' => $this->faker->sentence(),
            'es' => $this->faker->sentence().' (ES)',
            'fr' => $this->faker->sentence().' (FR)',
        ];
        $firstAirDate = $this->faker->dateTimeBetween('-20 years', '-1 month');
        $lastAirDate = $this->faker->boolean(70)
            ? $this->faker->dateTimeBetween($firstAirDate, 'now')
            : null;

        return [
            'tmdb_id' => null,
            'imdb_id' => null,
            'slug' => Str::slug($nameEn).'-'.Str::lower(Str::random(6)),
            'name' => $nameEn,
            'name_translations' => $nameTranslations,
            'original_name' => $nameEn,
            'first_air_date' => $firstAirDate,
            'last_air_date' => $lastAirDate,
            'number_of_seasons' => $this->faker->numberBetween(1, 12),
            'number_of_episodes' => $this->faker->numberBetween(6, 240),
            'episode_run_time' => $this->faker->numberBetween(20, 75),
            'status' => $this->faker->randomElement(['Returning Series', 'Ended', 'Planned']),
            'overview' => $overviewTranslations['en'],
            'overview_translations' => $overviewTranslations,
            'tagline' => $taglineTranslations['en'],
            'tagline_translations' => $taglineTranslations,
            'homepage' => $this->faker->url(),
            'popularity' => $this->faker->randomFloat(3, 0, 1000),
            'vote_average' => $this->faker->randomFloat(1, 0, 10),
            'vote_count' => $this->faker->numberBetween(0, 25000),
            'poster_path' => $this->faker->imageUrl(300, 450),
            'backdrop_path' => $this->faker->imageUrl(1280, 720),
            'media_type' => 'tv',
            'adult' => false,
        ];
    }
}
