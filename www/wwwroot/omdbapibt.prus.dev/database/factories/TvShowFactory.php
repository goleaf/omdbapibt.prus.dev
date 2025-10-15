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
        $firstAirDate = $this->faker->dateTimeBetween('-20 years', 'now');
        $lastAirDate = $this->faker->optional()->dateTimeBetween($firstAirDate, 'now');

        return [
            'tmdb_id' => $this->faker->unique()->numberBetween(1, 100_000_000),
            'imdb_id' => sprintf('tt%07d', $this->faker->unique()->numberBetween(1, 9_999_999)),
            'slug' => Str::slug($name . '-' . Str::lower(Str::random(6))),
            'name' => $name,
            'original_name' => $this->faker->optional()->sentence(3),
            'first_air_date' => $firstAirDate,
            'last_air_date' => $lastAirDate,
            'overview' => $this->faker->paragraph(),
            'number_of_seasons' => $this->faker->numberBetween(1, 10),
            'number_of_episodes' => $this->faker->numberBetween(6, 120),
            'status' => $this->faker->randomElement(['Ended', 'Returning Series', 'In Production']),
            'popularity' => $this->faker->randomFloat(3, 0, 1000),
            'vote_average' => $this->faker->randomFloat(1, 0, 10),
            'vote_count' => $this->faker->numberBetween(0, 10_000),
            'poster_path' => $this->faker->optional()->lexify('poster_????.jpg'),
            'backdrop_path' => $this->faker->optional()->lexify('backdrop_????.jpg'),
        ];
    }
}
