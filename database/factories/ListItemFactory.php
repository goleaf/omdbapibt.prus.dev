<?php

namespace Database\Factories;

use App\Models\ListItem;
use App\Models\ListModel;
use App\Models\Movie;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ListItem>
 */
class ListItemFactory extends Factory
{
    protected $model = ListItem::class;

    public function definition(): array
    {
        return [
            'list_id' => ListModel::factory(),
            'movie_id' => Movie::factory(),
            'position' => $this->faker->numberBetween(1, 25),
        ];
    }
}
