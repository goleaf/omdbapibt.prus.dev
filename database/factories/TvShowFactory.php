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
        $name = $this->faker->unique()->sentence(3);
        $nameEs = fake('es_ES')->sentence(3);
        $nameFr = fake('fr_FR')->sentence(3);

        $overviewEn = $this->faker->paragraph();
        $overviewEs = fake('es_ES')->paragraph();
        $overviewFr = fake('fr_FR')->paragraph();

        $taglineEn = $this->faker->sentence();
        $taglineEs = fake('es_ES')->sentence();
        $taglineFr = fake('fr_FR')->sentence();
        $firstAirDate = $this->faker->dateTimeBetween('-20 years', '-1 month');
        $lastAirDate = $this->faker->boolean(70)
            ? $this->faker->dateTimeBetween($firstAirDate, 'now')
            : null;

        return [
            'tmdb_id' => null,
            'imdb_id' => null,
            'slug' => Str::slug($name).'-'.Str::lower(Str::random(6)),
            'name' => $name,
            'name_translations' => [
                'en' => $name,
                'es' => $nameEs,
                'fr' => $nameFr,
            ],
            'original_name' => $name,
            'first_air_date' => $firstAirDate,
            'last_air_date' => $lastAirDate,
            'number_of_seasons' => $this->faker->numberBetween(1, 12),
            'number_of_episodes' => $this->faker->numberBetween(6, 240),
            'episode_run_time' => $this->faker->numberBetween(20, 75),
            'status' => $this->faker->randomElement(['Returning Series', 'Ended', 'Planned']),
            'overview' => $overviewEn,
            'overview_translations' => [
                'en' => $overviewEn,
                'es' => $overviewEs,
                'fr' => $overviewFr,
            ],
            'tagline' => $taglineEn,
            'tagline_translations' => [
                'en' => $taglineEn,
                'es' => $taglineEs,
                'fr' => $taglineFr,
            ],
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
