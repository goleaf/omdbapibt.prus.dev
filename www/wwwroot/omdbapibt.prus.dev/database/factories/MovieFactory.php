<?php

namespace Database\Factories;

use App\Models\Movie;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Movie>
 */
class MovieFactory extends Factory
{
    protected $model = Movie::class;

    public function definition(): array
    {
        return [
            'tmdb_id' => $this->faker->unique()->numberBetween(1, 999999),
            'imdb_id' => 'tt'.$this->faker->unique()->numberBetween(1000000, 9999999),
            'omdb_id' => (string) $this->faker->uuid(),
            'slug' => Str::slug($this->faker->unique()->sentence(3)),
            'title' => $this->faker->sentence(3),
            'original_title' => $this->faker->sentence(3),
            'year' => $this->faker->year(),
            'runtime' => $this->faker->numberBetween(80, 180),
            'release_date' => $this->faker->date(),
            'plot' => $this->faker->paragraph(),
            'tagline' => $this->faker->sentence(),
            'homepage' => $this->faker->url(),
            'budget' => $this->faker->numberBetween(1000000, 500000000),
            'revenue' => $this->faker->numberBetween(2000000, 900000000),
            'status' => 'Released',
            'popularity' => $this->faker->randomFloat(3, 1, 1000),
            'vote_average' => $this->faker->randomFloat(1, 1, 10),
            'vote_count' => $this->faker->numberBetween(1, 10000),
            'poster_path' => $this->faker->imageUrl(),
            'backdrop_path' => $this->faker->imageUrl(),
            'trailer_url' => $this->faker->url(),
            'media_type' => 'movie',
            'adult' => false,
            'video' => false,
        ];
    }
}
