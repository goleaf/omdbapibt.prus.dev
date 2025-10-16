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
        $name = ucwords($this->faker->unique()->words(2, true));

        return [
            'name' => $name,
            'code' => $this->faker->unique()->languageCode(),
            'native_name' => $this->faker->words(2, true),
            'active' => $this->faker->boolean(90),
        ];
    }
}
