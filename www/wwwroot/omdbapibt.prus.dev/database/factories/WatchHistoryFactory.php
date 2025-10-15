<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\WatchHistory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends Factory<\App\Models\WatchHistory>
 */
class WatchHistoryFactory extends Factory
{
    protected $model = WatchHistory::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $status = $this->faker->randomElement(['completed', 'in_progress']);

        return [
            'user_id' => User::factory(),
            'watchable_type' => User::class,
            'watchable_id' => fn (array $attributes) => $attributes['user_id'],
            'content_title' => $this->faker->sentence(3),
            'content_type' => $this->faker->randomElement(['movie', 'tv']),
            'status' => $status,
            'progress_percent' => $status === 'completed' ? 100 : $this->faker->numberBetween(1, 95),
            'duration_seconds' => $this->faker->numberBetween(600, 7200),
            'viewed_at' => Carbon::now()->subDays($this->faker->numberBetween(0, 365)),
            'metadata' => [
                'device' => $this->faker->randomElement(['web', 'mobile', 'tv']),
            ],
        ];
    }
}
