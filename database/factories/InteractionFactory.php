<?php

namespace Database\Factories;

use App\Models\Interaction;
use App\Models\Movie;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Interaction>
 */
class InteractionFactory extends Factory
{
    protected $model = Interaction::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'movie_id' => Movie::factory(),
            'type' => $this->faker->randomElement(['viewed', 'shared', 'wishlisted']),
            'payload' => [
                'referrer' => $this->faker->optional()->url(),
            ],
            'occurred_at' => $this->faker->dateTimeBetween('-2 weeks', 'now'),
        ];
    }
}
