<?php

namespace Database\Factories;

use App\Models\Movie;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Movie>
 */
class MovieFactory extends Factory
{
    protected $model = Movie::class;

    public function definition(): array
    {
        $title = $this->faker->unique()->sentence(3);

        return [
            'tmdb_id' => $this->faker->unique()->numberBetween(1_000, 9_999_999),
            'imdb_id' => 'tt'.$this->faker->unique()->numerify('#######'),
            'omdb_id' => 'omdb-'.$this->faker->unique()->numerify('######'),
            'slug' => Str::slug($title.'-'.$this->faker->unique()->numerify('####')),
            'title' => $title,
            'original_title' => $title,
            'year' => (int) $this->faker->year(),
            'runtime' => $this->faker->numberBetween(80, 180),
            'release_date' => $this->faker->dateTimeBetween('-20 years', 'now'),
            'plot' => $this->faker->paragraph(),
            'tagline' => $this->faker->sentence(),
            'homepage' => $this->faker->url(),
            'budget' => $this->faker->numberBetween(100_000, 200_000_000),
            'revenue' => $this->faker->numberBetween(100_000, 500_000_000),
            'status' => $this->faker->randomElement(['Released', 'Post Production', 'In Production']),
            'popularity' => $this->faker->randomFloat(3, 0, 1_000),
            'vote_average' => $this->faker->randomFloat(1, 0, 10),
            'vote_count' => $this->faker->numberBetween(0, 10_000),
            'poster_path' => $this->faker->imageUrl(600, 900, 'movie'),
            'backdrop_path' => $this->faker->imageUrl(1280, 720, 'movie'),
            'trailer_url' => $this->faker->url(),
            'media_type' => 'movie',
            'adult' => false,
            'video' => false,
            'translations' => [
                'title' => [
                    'en' => $title,
                ],
            ],
            'cast' => [],
            'crew' => [],
            'streaming_links' => [],
            'trailers' => [],
        ];
    }
}
