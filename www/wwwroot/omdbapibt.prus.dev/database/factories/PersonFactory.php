<?php

namespace Database\Factories;

use App\Models\Person;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Person>
 */
class PersonFactory extends Factory
{
    protected $model = Person::class;

    public function definition(): array
    {
        return [
            'tmdb_id' => $this->faker->unique()->numberBetween(1, 999999),
            'imdb_id' => 'nm'.$this->faker->unique()->numberBetween(1000000, 9999999),
            'name' => $this->faker->name(),
            'biography' => $this->faker->paragraph(),
            'birthday' => $this->faker->date(),
            'deathday' => null,
            'place_of_birth' => $this->faker->city().', '.$this->faker->country(),
            'gender' => $this->faker->randomElement(['Male', 'Female', 'Non-binary']),
            'known_for_department' => $this->faker->randomElement(['Acting', 'Directing', 'Writing']),
            'popularity' => $this->faker->randomFloat(3, 1, 1000),
            'profile_path' => $this->faker->imageUrl(),
        ];
    }
}
