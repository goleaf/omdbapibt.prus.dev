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
        $title = $this->faker->unique()->sentence(3);
        $releaseDate = $this->faker->dateTimeBetween('-30 years', 'now');

        return [
            'tmdb_id' => $this->faker->unique()->numberBetween(1, 100_000_000),
            'imdb_id' => sprintf('tt%07d', $this->faker->unique()->numberBetween(1, 9_999_999)),
            'omdb_id' => $this->faker->optional()->lexify('omdb????'),
            'slug' => Str::slug($title . '-' . Str::lower(Str::random(6))),
            'title' => $title,
            'original_title' => $this->faker->optional()->sentence(3),
            'year' => (int) $releaseDate->format('Y'),
            'runtime' => $this->faker->numberBetween(60, 180),
            'release_date' => $releaseDate,
            'plot' => $this->faker->paragraph(),
            'tagline' => $this->faker->optional()->sentence(),
            'homepage' => $this->faker->optional()->url(),
            'budget' => $this->faker->optional()->numberBetween(1_000_000, 200_000_000),
            'revenue' => $this->faker->optional()->numberBetween(1_000_000, 1_000_000_000),
            'status' => $this->faker->randomElement(['Released', 'Post Production']),
            'popularity' => $this->faker->randomFloat(3, 0, 1000),
            'vote_average' => $this->faker->randomFloat(1, 0, 10),
            'vote_count' => $this->faker->numberBetween(0, 10_000),
            'poster_path' => $this->faker->optional()->lexify('poster_????.jpg'),
            'backdrop_path' => $this->faker->optional()->lexify('backdrop_????.jpg'),
            'trailer_url' => $this->faker->optional()->url(),
            'media_type' => 'movie',
            'adult' => $this->faker->boolean(5),
            'video' => false,
        ];
    }
}
