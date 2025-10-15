<?php

namespace Database\Factories;

use App\Models\Movie;
use App\Models\TvShow;
use App\Models\User;
use App\Models\WatchHistory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WatchHistory>
 */
class WatchHistoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<WatchHistory>
     */
    protected $model = WatchHistory::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'watchable_type' => Movie::class,
            'watchable_id' => Movie::factory(),
            'watched_at' => $this->faker->dateTimeBetween('-2 months', 'now'),
        ];
    }

    /**
     * Indicate that the watch history entry belongs to a TV show.
     */
    public function forTvShow(): self
    {
        return $this->state(fn () => [
            'watchable_type' => TvShow::class,
            'watchable_id' => TvShow::factory(),
        ]);
    }

    /**
     * Indicate that the watch history entry belongs to a movie.
     */
    public function forMovie(): self
    {
        return $this->state(fn () => [
            'watchable_type' => Movie::class,
            'watchable_id' => Movie::factory(),
        ]);
    }
}
