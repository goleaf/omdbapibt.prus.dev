<?php

namespace Database\Factories;

use App\Models\Language;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Language>
 */
class LanguageFactory extends Factory
{
    protected $model = Language::class;

    public function definition(): array
    {
        $identifier = $this->faker->unique()->numberBetween(1, 9_999);
        $baseName = 'Language '.$identifier;

        $nameTranslations = [
            'en' => $baseName,
            'es' => 'Idioma '.$identifier,
            'fr' => 'Langue '.$identifier,
        ];

        $nativeTranslations = [
            'en' => $baseName,
            'es' => 'Lengua '.$identifier,
            'fr' => 'Langue maternelle '.$identifier,
        ];

        return [
            'name' => $nameTranslations['en'],
            'name_translations' => $nameTranslations,
            'code' => 'l'.str_pad((string) $identifier, 4, '0', STR_PAD_LEFT),
            'native_name' => $nativeTranslations['en'],
            'native_name_translations' => $nativeTranslations,
            'active' => $this->faker->boolean(90),
        ];
    }
}
