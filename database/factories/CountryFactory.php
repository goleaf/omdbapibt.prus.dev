<?php

namespace Database\Factories;

use App\Models\Country;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Country>
 */
class CountryFactory extends Factory
{
    protected $model = Country::class;

    public function definition(): array
    {
        $identifier = $this->faker->unique()->numberBetween(1, 99_999);
        $baseName = 'Country '.$identifier;

        $translations = [
            'en' => $baseName,
            'es' => 'País '.$identifier,
            'fr' => 'Pays '.$identifier,
        ];

        $codeValue = strtoupper(str_pad(base_convert((string) ($identifier % 1296), 10, 36), 2, '0', STR_PAD_LEFT));

        return [
            'name_translations' => $translations,
            'code' => $codeValue,
            'active' => $this->faker->boolean(90),
        ];
    }
}
