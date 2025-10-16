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
        $identifier = $this->faker->unique()->numberBetween(1, 99_999);
        $baseName = 'Genre '.$identifier;

        $translations = [
            'en' => $baseName,
            'es' => 'Género '.$identifier,
            'fr' => 'Genre '.$identifier,
        ];

        return [
            'name' => $translations['en'],
            'name_translations' => $translations,
            'slug' => Str::slug($baseName).'-'.$identifier,
            'tmdb_id' => 10_000 + $identifier,
        ];
    }

    public function named(string $name, ?int $tmdbId = null): self
    {
        return $this->state(function () use ($name, $tmdbId): array {
            $translations = [
                'en' => $name,
                'es' => $name,
                'fr' => $name,
            ];

            return [
                'name' => $translations['en'],
                'name_translations' => $translations,
                'slug' => Str::slug($name),
                'tmdb_id' => $tmdbId ?? $this->faker->unique()->numberBetween(1, 9_999),
            ];
        });
    }
}
