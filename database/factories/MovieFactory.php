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
        $title = $this->faker->sentence(3);

        $titles = [
            'en' => $title,
            'es' => $title.' (ES)',
            'fr' => $title.' (FR)',
        ];

        return [
            'tmdb_id' => $this->faker->unique()->numberBetween(1, 10_000_000),
            'imdb_id' => 'tt'.$this->faker->unique()->numerify('########'),
            'omdb_id' => $this->faker->optional()->uuid(),
            'slug' => Str::slug($title).'-'.$this->faker->unique()->numerify('####'),
            'title' => $titles,
            'original_title' => $title,
            'year' => (int) $this->faker->year(),
            'runtime' => $this->faker->numberBetween(80, 180),
            'release_date' => $this->faker->date(),
            'overview' => [
                'en' => $this->faker->paragraph(),
                'es' => $this->faker->paragraph(),
                'fr' => $this->faker->paragraph(),
            ],
            'translation_metadata' => [
                'title' => $titles,
                'access' => [
                    'requires_subscription' => false,
                ],
            ],
            'credits' => [
                'cast' => [
                    ['name' => $this->faker->name(), 'character' => $this->faker->firstName()],
                ],
                'crew' => [
                    ['name' => $this->faker->name(), 'department' => 'Directing', 'job' => 'Director'],
                ],
            ],
            'streaming_links' => [
                ['provider' => 'FluxStream', 'quality' => '4K'],
                ['provider' => 'CinePrime', 'quality' => 'HD'],
            ],
            'trailers' => [
                ['title' => 'Official Trailer', 'url' => $this->faker->url()],
            ],
            'plot' => $this->faker->paragraph(),
            'tagline' => $this->faker->sentence(),
            'homepage' => $this->faker->url(),
            'budget' => $this->faker->numberBetween(1_000_000, 200_000_000),
            'revenue' => $this->faker->numberBetween(1_000_000, 300_000_000),
            'status' => $this->faker->randomElement(['Released', 'Post Production']),
            'popularity' => $this->faker->randomFloat(3, 0, 500),
            'vote_average' => $this->faker->randomFloat(1, 0, 10),
            'vote_count' => $this->faker->numberBetween(0, 10_000),
            'poster_path' => $this->faker->imageUrl(),
            'backdrop_path' => $this->faker->imageUrl(),
            'trailer_url' => $this->faker->url(),
            'media_type' => 'movie',
            'adult' => $this->faker->boolean(10),
            'video' => $this->faker->boolean(10),
        ];
    }
}
