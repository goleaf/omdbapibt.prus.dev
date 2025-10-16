<?php

namespace Database\Factories;

use App\Models\TvShow;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

use function fake;

/**
 * @extends Factory<TvShow>
 */
class TvShowFactory extends Factory
{
    protected $model = TvShow::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->sentence(3);
        $spanishFaker = fake('es_ES');
        $firstAirDate = $this->faker->dateTimeBetween('-20 years', '-1 month');
        $lastAirDate = $this->faker->boolean(70)
            ? $this->faker->dateTimeBetween($firstAirDate, 'now')
            : null;

        $nameTranslations = [
            'en' => $name,
            'es' => $spanishFaker->sentence(3),
        ];

        $overview = $this->faker->paragraph();
        $overviewTranslations = [
            'en' => $overview,
            'es' => $spanishFaker->paragraph(),
        ];

        $tagline = $this->faker->sentence();
        $taglineTranslations = [
            'en' => $tagline,
            'es' => $spanishFaker->sentence(),
        ];

        return [
            'tmdb_id' => null,
            'imdb_id' => null,
            'slug' => Str::slug($name).'-'.Str::lower(Str::random(6)),
            'name' => $name,
            'name_translations' => $nameTranslations,
            'original_name' => $name,
            'first_air_date' => $firstAirDate,
            'last_air_date' => $lastAirDate,
            'number_of_seasons' => $this->faker->numberBetween(1, 12),
            'number_of_episodes' => $this->faker->numberBetween(6, 240),
            'episode_run_time' => $this->faker->numberBetween(20, 75),
            'status' => $this->faker->randomElement(['Returning Series', 'Ended', 'Planned']),
            'overview' => $overview,
            'overview_translations' => $overviewTranslations,
            'tagline' => $tagline,
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
