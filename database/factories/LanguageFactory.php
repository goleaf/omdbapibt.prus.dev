<?php

namespace Database\Factories;

use App\Models\Language;
use Faker\Factory as FakerFactory;
use Faker\Generator;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Language>
 */
class LanguageFactory extends Factory
{
    protected $model = Language::class;

    /**
     * Localized faker cache.
     *
     * @var array<string, Generator>
     */
    protected static array $localizedFakers = [];

    public function definition(): array
    {
        $englishName = Str::title($this->faker->unique()->words(2, true));
        $nativeEnglish = Str::title($this->faker->words(2, true));

        return [
            'name_translations' => $this->localizedSet($englishName),
            'native_name_translations' => $this->localizedSet($nativeEnglish),
            'code' => Str::upper($this->faker->unique()->lexify('????')),
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

    protected function fakerForLocale(string $locale): Generator
    {
        return self::$localizedFakers[$locale] ??= FakerFactory::create($locale);
    }
}
