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

        $titleTranslations = collect(['en', 'es', 'fr'])->mapWithKeys(function (string $locale) use ($title) {
            return [$locale => $locale === 'en' ? $title : $this->faker->sentence(3)];
        })->all();

        $translations = collect($titleTranslations)->map(function (string $translatedTitle, string $locale) {
            return [
                'title' => $translatedTitle,
                'tagline' => $this->faker->sentence(),
                'overview' => $this->faker->paragraph(),
            ];
        })->all();

        return [
            'tmdb_id' => $this->faker->unique()->numberBetween(1, 10_000_000),
            'imdb_id' => 'tt'.$this->faker->unique()->numerify('########'),
            'omdb_id' => $this->faker->optional()->uuid(),
            'slug' => Str::slug($title).'-'.$this->faker->unique()->numerify('####'),
            'title' => $titleTranslations,
            'original_title' => $title,
            'year' => (int) $this->faker->year(),
            'runtime' => $this->faker->numberBetween(80, 180),
            'release_date' => $this->faker->date(),
            'overview' => [
                'en' => $this->faker->paragraph(),
                'es' => $this->faker->paragraph(),
                'fr' => $this->faker->paragraph(),
            ],
            'translations' => $translations,
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
            'cast' => collect(range(1, 6))->map(fn () => [
                'name' => $this->faker->name(),
                'character' => $this->faker->name(),
                'order' => $this->faker->numberBetween(1, 10),
            ])->all(),
            'crew' => [
                [
                    'name' => $this->faker->name(),
                    'job' => 'Director',
                ],
                [
                    'name' => $this->faker->name(),
                    'job' => 'Writer',
                ],
                [
                    'name' => $this->faker->name(),
                    'job' => 'Producer',
                ],
            ],
            'streaming_links' => [
                [
                    'service' => 'Nebula+',
                    'type' => 'subscription',
                    'quality' => $this->faker->randomElement(['HD', '4K', 'SD']),
                    'url' => $this->faker->url(),
                ],
                [
                    'service' => 'CinePrime',
                    'type' => 'rent',
                    'quality' => $this->faker->randomElement(['HD', '4K']),
                    'url' => $this->faker->url(),
                ],
            ],
            'trailers' => [
                [
                    'name' => 'Official Trailer',
                    'site' => 'YouTube',
                    'url' => 'https://www.youtube.com/watch?v='.$this->faker->lexify('???????????'),
                    'thumbnail' => $this->faker->imageUrl(),
                ],
                [
                    'name' => 'Teaser',
                    'site' => 'YouTube',
                    'url' => 'https://www.youtube.com/watch?v='.$this->faker->lexify('???????????'),
                    'thumbnail' => $this->faker->imageUrl(),
                ],
            ],
            'media_type' => $this->faker->randomElement(['movie', 'tv']),
            'adult' => $this->faker->boolean(10),
            'video' => $this->faker->boolean(10),
        ];
    }
}
