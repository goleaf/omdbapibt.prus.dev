<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Review>
 */
class ReviewFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'movie_title' => $this->faker->sentence(3),
            'rating' => $this->faker->numberBetween(1, 5),
            'body' => '<p>' . implode('</p><p>', $this->faker->paragraphs(2)) . '</p>',
        ];
    }
}
