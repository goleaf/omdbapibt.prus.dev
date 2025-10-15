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
            'tmdb_id' => null,
            'imdb_id' => null,
            'omdb_id' => null,
            'slug' => Str::slug($title) . '-' . Str::lower(Str::random(6)),
            'title' => $title,
            'original_title' => $title,
            'year' => (int) $releaseDate->format('Y'),
            'runtime' => $this->faker->numberBetween(60, 180),
            'release_date' => $releaseDate,
            'plot' => $this->faker->paragraph(),
            'tagline' => $this->faker->sentence(),
            'homepage' => $this->faker->url(),
            'budget' => $this->faker->numberBetween(1_000_000, 100_000_000),
            'revenue' => $this->faker->numberBetween(1_000_000, 300_000_000),
            'status' => $this->faker->randomElement(['Released', 'Planned']),
            'popularity' => $this->faker->randomFloat(3, 0, 1000),
            'vote_average' => $this->faker->randomFloat(1, 0, 10),
            'vote_count' => $this->faker->numberBetween(0, 25000),
            'poster_path' => $this->faker->imageUrl(300, 450),
            'backdrop_path' => $this->faker->imageUrl(1280, 720),
            'trailer_url' => $this->faker->url(),
            'media_type' => 'movie',
            'adult' => false,
            'video' => false,
        ];
    }
}
