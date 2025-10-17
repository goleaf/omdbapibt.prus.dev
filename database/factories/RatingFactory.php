<?php

namespace Database\Factories;

use App\Models\Movie;
use App\Models\Rating;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Rating>
 */
class RatingFactory extends Factory
{
    protected $model = Rating::class;

    public function definition(): array
    {
        $liked = $this->faker->boolean(40);
        $disliked = $liked ? false : $this->faker->boolean(25);

        return [
            'user_id' => User::factory(),
            'movie_id' => Movie::factory(),
            'rating' => $this->faker->numberBetween(1, 10),
            'liked' => $liked,
            'disliked' => $disliked,
            'rated_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
