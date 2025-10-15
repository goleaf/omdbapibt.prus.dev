<?php

namespace Database\Factories;

use App\Models\Person;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Person>
 */
class PersonFactory extends Factory
{
    protected $model = Person::class;

    public function definition(): array
    {
        $name = $this->faker->name();

        return [
            'tmdb_id' => $this->faker->unique()->numberBetween(1, 100_000_000),
            'imdb_id' => sprintf('nm%07d', $this->faker->unique()->numberBetween(1, 9_999_999)),
            'slug' => Str::slug($name . '-' . Str::lower(Str::random(6))),
            'name' => $name,
            'also_known_as' => [$this->faker->name(), $this->faker->name()],
            'biography' => [
                'en' => $this->faker->paragraph(),
            ],
            'birthday' => $this->faker->dateTimeBetween('-70 years', '-18 years'),
            'deathday' => null,
            'place_of_birth' => $this->faker->city(),
            'gender' => $this->faker->randomElement([1, 2, 3, null]),
            'known_for_department' => $this->faker->randomElement(['Acting', 'Directing', 'Production']),
            'popularity' => $this->faker->randomFloat(3, 0, 1000),
            'homepage' => $this->faker->url(),
            'profile_path' => $this->faker->optional()->lexify('profile_????.jpg'),
        ];
    }
}
