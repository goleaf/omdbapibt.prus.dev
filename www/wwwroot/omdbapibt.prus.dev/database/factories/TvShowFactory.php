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
        return [
            'tmdb_id' => $this->faker->unique()->numberBetween(1, 999999),
            'imdb_id' => 'tt'.$this->faker->unique()->numberBetween(1000000, 9999999),
            'slug' => Str::slug($this->faker->unique()->sentence(3)),
            'name' => $this->faker->sentence(3),
            'original_name' => $this->faker->sentence(3),
            'first_air_date' => $this->faker->date(),
            'last_air_date' => $this->faker->date(),
            'number_of_seasons' => $this->faker->numberBetween(1, 10),
            'number_of_episodes' => $this->faker->numberBetween(6, 120),
            'episode_run_time' => $this->faker->numberBetween(20, 90),
            'status' => 'Running',
            'overview' => $this->faker->paragraph(),
            'tagline' => $this->faker->sentence(),
            'homepage' => $this->faker->url(),
            'popularity' => $this->faker->randomFloat(3, 1, 1000),
            'vote_average' => $this->faker->randomFloat(1, 1, 10),
            'vote_count' => $this->faker->numberBetween(1, 10000),
            'poster_path' => $this->faker->imageUrl(),
            'backdrop_path' => $this->faker->imageUrl(),
            'media_type' => 'tv',
            'adult' => false,
        ];
    }
}
