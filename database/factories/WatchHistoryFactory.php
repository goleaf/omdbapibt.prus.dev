<?php

namespace Database\Factories;

use App\Models\Movie;
use App\Models\TvShow;
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
            'watchable_type' => Movie::class,
            'watchable_id' => Movie::factory(),
            'progress_percent' => $this->faker->numberBetween(10, 100),
            'completed' => $this->faker->boolean(70),
            'watched_at' => $this->faker->dateTimeBetween('-45 days', 'now'),
        ];
    }

    public function forMovie(?Movie $movie = null): self
    {
        return $this->state(fn (): array => [
            'watchable_type' => Movie::class,
        ])->for($movie ?? Movie::factory(), 'watchable');
    }

    public function forTvShow(?TvShow $show = null): self
    {
        return $this->state(fn (): array => [
            'watchable_type' => TvShow::class,
        ])->for($show ?? TvShow::factory(), 'watchable');
    }
}
