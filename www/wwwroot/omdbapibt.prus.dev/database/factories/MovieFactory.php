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
            'tmdb_id' => $this->faker->unique()->numberBetween(1, 1_000_000),
            'imdb_id' => 'tt'.$this->faker->unique()->numerify('########'),
            'omdb_id' => (string) Str::uuid(),
            'slug' => $this->faker->unique()->slug(),
            'title' => $this->faker->sentence(3),
            'original_title' => $this->faker->sentence(3),
            'year' => (int) $this->faker->year(),
            'runtime' => $this->faker->numberBetween(60, 200),
            'release_date' => $this->faker->date(),
            'plot' => $this->faker->paragraph(),
            'tagline' => $this->faker->sentence(),
            'homepage' => $this->faker->url(),
            'budget' => $this->faker->numberBetween(100_000, 500_000_000),
            'revenue' => $this->faker->numberBetween(100_000, 500_000_000),
            'status' => $this->faker->randomElement(['Released', 'Post Production', 'Planned']),
            'popularity' => $this->faker->randomFloat(3, 0, 500),
            'vote_average' => $this->faker->randomFloat(1, 0, 10),
            'vote_count' => $this->faker->numberBetween(0, 100000),
            'poster_path' => $this->faker->imageUrl(),
            'backdrop_path' => $this->faker->imageUrl(),
            'trailer_url' => $this->faker->url(),
            'media_type' => $this->faker->randomElement(['movie', 'tv']),
            'adult' => $this->faker->boolean(),
            'video' => $this->faker->boolean(),
        ];
    }
}
