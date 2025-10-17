<?php

namespace Database\Factories;

use App\Models\ListModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ListModel>
 */
class ListModelFactory extends Factory
{
    protected $model = ListModel::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => $this->faker->words(3, true),
            'public' => $this->faker->boolean(25),
            'description' => $this->faker->optional()->sentence(),
            'cover_url' => $this->faker->optional()->imageUrl(),
        ];
    }

    public function watchLater(): self
    {
        return $this->state([
            'title' => ListModel::WATCH_LATER_TITLE,
            'public' => false,
            'description' => null,
            'cover_url' => null,
        ]);
    }
}
