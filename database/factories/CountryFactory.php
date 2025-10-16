<?php

namespace Database\Factories;

use App\Models\Country;
use Faker\Factory as FakerFactory;
use Faker\Generator;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Country>
 */
class CountryFactory extends Factory
{
    protected $model = Country::class;

    /**
     * Localized faker cache.
     *
     * @var array<string, Generator>
     */
    protected static array $localizedFakers = [];

    protected static int $codeSequence = 0;

    public function definition(): array
    {
        $englishName = Str::title($this->faker->unique()->words(2, true));

        return [
            'name_translations' => $this->localizedSet($englishName),
            'code' => $this->nextCode(),
            'active' => $this->faker->boolean(90),
        ];
    }

    protected function localizedSet(string $english, bool $withFaker = true): array
    {
        $translations = [
            'en' => $english,
        ];

        $translations['es'] = $withFaker
            ? Str::title($this->fakerForLocale('es_ES')->words(2, true))
            : $english;

        $translations['fr'] = $withFaker
            ? Str::title($this->fakerForLocale('fr_FR')->words(2, true))
            : $english;

        return $translations;
    }

    protected function nextCode(): string
    {
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $base = strlen($alphabet);
        $index = self::$codeSequence++;

        $first = $alphabet[intdiv($index, $base) % $base] ?? $alphabet[0];
        $second = $alphabet[$index % $base] ?? $alphabet[0];

        return $first.$second;
    }

    protected function fakerForLocale(string $locale): Generator
    {
        return self::$localizedFakers[$locale] ??= FakerFactory::create($locale);
    }
}
