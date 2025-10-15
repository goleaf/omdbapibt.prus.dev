<?php

namespace Database\Factories;

use App\Models\Movie;
use App\Models\WatchHistory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<WatchHistory>
 */
class WatchHistoryFactory extends Factory
{
    protected $model = WatchHistory::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'movie_id' => Movie::factory(),
            'watched_at' => $this->faker->dateTimeBetween('-1 year'),
            'progress' => $this->faker->numberBetween(0, 100),
            'user_rating' => $this->faker->numberBetween(1, 10),
        ];
    }
}
