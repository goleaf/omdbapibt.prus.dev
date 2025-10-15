<?php

namespace Database\Factories;

use App\Models\Movie;
use App\Models\User;
use App\Models\WatchHistory;
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
            'watched_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'rewatch_count' => $this->faker->numberBetween(1, 5),
        ];
    }
}
