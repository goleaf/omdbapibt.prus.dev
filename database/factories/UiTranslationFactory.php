<?php

namespace Database\Factories;

use App\Models\UiTranslation;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<UiTranslation>
 */
class UiTranslationFactory extends Factory
{
    protected $model = UiTranslation::class;

    public function definition(): array
    {
        $group = $this->faker->randomElement(['nav', 'dashboard', 'filters', 'custom']);
        $key = Str::slug($this->faker->unique()->words(2, true), '_');
        $label = ucfirst(str_replace('_', ' ', $key));

        return [
            'group' => $group,
            'key' => $key,
            'value' => [
                'en' => $label,
                'es' => $label.' ES',
                'fr' => $label.' FR',
            ],
        ];
    }
}
