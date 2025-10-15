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
        $name = $this->faker->unique()->words(2, true);

        return [
            'name' => Str::title($name),
            'slug' => Str::slug($name).'-'.$this->faker->unique()->numerify('##'),
            'tmdb_id' => $this->faker->unique()->numberBetween(1, 9_999),
        ];
    }

    public function named(string $name, ?int $tmdbId = null): self
    {
        return $this->state(function () use ($name, $tmdbId): array {
            return [
                'name' => $name,
                'slug' => Str::slug($name),
                'tmdb_id' => $tmdbId ?? $this->faker->unique()->numberBetween(1, 9_999),
            ];
        });
    }
}
