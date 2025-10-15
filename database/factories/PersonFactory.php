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
        $birthday = $this->faker->dateTimeBetween('-80 years', '-20 years');
        $deathday = $this->faker->boolean(20)
            ? $this->faker->dateTimeBetween($birthday, '-1 years')
            : null;

        return [
            'tmdb_id' => null,
            'imdb_id' => null,
            'slug' => Str::slug($name).'-'.Str::lower(Str::random(6)),
            'name' => $name,
            'biography' => $this->faker->paragraph(),
            'biography_translations' => [
                'en' => $this->faker->paragraph(),
                'es' => $this->faker->paragraph(),
                'fr' => $this->faker->paragraph(),
            ],
            'birthday' => $birthday,
            'deathday' => $deathday,
            'place_of_birth' => $this->faker->city(),
            'gender' => $this->faker->numberBetween(0, 3),
            'known_for_department' => $this->faker->randomElement(['Acting', 'Directing', 'Writing', 'Production']),
            'popularity' => $this->faker->randomFloat(3, 0, 1000),
            'profile_path' => $this->faker->imageUrl(300, 450),
            'poster_path' => $this->faker->imageUrl(500, 750),
        ];
    }
}
