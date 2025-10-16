<?php

namespace Database\Factories;

use App\Models\Genre;
use Faker\Factory as FakerFactory;
use Faker\Generator;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Genre>
 */
class GenreFactory extends Factory
{
    protected $model = Genre::class;

    /**
     * Localized faker cache.
     *
     * @var array<string, Generator>
     */
    protected static array $localizedFakers = [];

    public function definition(): array
    {
        $englishName = Str::title($this->faker->unique()->words(3, true));

        return [
            'name_translations' => $this->localizedSet($englishName),
            'slug' => Str::slug($englishName),
            'tmdb_id' => $this->faker->unique()->numberBetween(1, 1_000_000),
        ];
    }

    public function named(string $name, ?int $tmdbId = null): self
    {
        return $this->state(function () use ($name, $tmdbId): array {
            return [
                'name_translations' => $this->localizedSet($name, false),
                'slug' => Str::slug($name),
                'tmdb_id' => $tmdbId ?? $this->faker->unique()->numberBetween(1, 1_000_000),
            ];
        });
    }

    protected function localizedSet(string $english, bool $withFaker = true): array
    {
        $translations = [
            'en' => $english,
        ];

        $translations['es'] = $withFaker
            ? Str::title($this->fakerForLocale('es_ES')->words(3, true))
            : $english;

        $translations['fr'] = $withFaker
            ? Str::title($this->fakerForLocale('fr_FR')->words(3, true))
            : $english;

        return $translations;
    }

    protected function fakerForLocale(string $locale): Generator
    {
        return self::$localizedFakers[$locale] ??= FakerFactory::create($locale);
    }
}
