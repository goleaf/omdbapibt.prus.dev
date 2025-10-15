<?php

namespace Database\Factories;

use App\Enums\ParserEntryStatus;
use App\Models\Movie;
use App\Models\ParserEntry;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ParserEntry>
 */
class ParserEntryFactory extends Factory
{
    protected $model = ParserEntry::class;

    public function definition(): array
    {
        $title = $this->faker->sentence(3);

        $payload = [
            'title' => [
                'en' => $title,
            ],
            'overview' => [
                'en' => $this->faker->paragraph(),
            ],
            'popularity' => $this->faker->randomFloat(3, 0, 500),
            'vote_average' => $this->faker->randomFloat(1, 0, 10),
        ];

        return [
            'subject_type' => Movie::class,
            'subject_id' => Movie::factory(),
            'parser' => $this->faker->randomElement(['tmdb', 'omdb']),
            'payload' => $payload,
            'baseline_snapshot' => [
                'title' => [
                    'en' => $this->faker->sentence(3),
                ],
                'overview' => [
                    'en' => $this->faker->paragraph(),
                ],
                'popularity' => $this->faker->randomFloat(3, 0, 500),
                'vote_average' => $this->faker->randomFloat(1, 0, 10),
            ],
            'status' => ParserEntryStatus::Pending,
            'notes' => null,
        ];
    }

    public function approved(): self
    {
        return $this->state(fn (): array => ['status' => ParserEntryStatus::Approved]);
    }

    public function rejected(): self
    {
        return $this->state(fn (): array => ['status' => ParserEntryStatus::Rejected]);
    }
}
