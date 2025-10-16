<?php

namespace Database\Factories;

use App\Models\Genre;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Genre>
 */
class GenreFactory extends Factory
{
    protected $model = Genre::class;

    public function definition(): array
    {
        $name = ucfirst($this->faker->unique()->words(2, true));

        return [
            'name' => $name,
            'name_translations' => [
                'en' => $name,
            ],
            'slug' => Str::slug($name),
            'tmdb_id' => $this->faker->unique()->numberBetween(1, 20_000),
        ];
    }

    public function named(string $name, int $tmdbId): self
    {
        return $this->state(fn (array $attributes) => [
            'name' => $name,
            'name_translations' => [
                'en' => $name,
                'es' => $name,
            ],
            'slug' => Str::slug($name),
            'tmdb_id' => $tmdbId,
        ]);
    }
}
