<?php

namespace Database\Factories;

use App\Models\OmdbApiKeyProgress;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OmdbApiKeyProgress>
 */
class OmdbApiKeyProgressFactory extends Factory
{
    protected $model = OmdbApiKeyProgress::class;

    public function definition(): array
    {
        return [
            'sequence_cursor' => (string) $this->faker->numberBetween(1000, 999999),
        ];
    }
}
