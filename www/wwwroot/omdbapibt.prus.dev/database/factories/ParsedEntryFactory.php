<?php

namespace Database\Factories;

use App\Models\ParsedEntry;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ParsedEntry>
 */
class ParsedEntryFactory extends Factory
{
    protected $model = ParsedEntry::class;

    public function definition(): array
    {
        $original = [
            'title' => $this->faker->sentence(3),
            'year' => $this->faker->year(),
        ];

        $parsed = $original;
        $parsed['title'] = $this->faker->sentence(4);

        return [
            'original_payload' => $original,
            'parsed_payload' => $parsed,
            'status' => 'pending',
            'is_published' => false,
        ];
    }

    public function approved(): self
    {
        return $this->state(fn () => [
            'status' => 'approved',
            'is_published' => true,
        ]);
    }

    public function rejected(): self
    {
        return $this->state(fn () => [
            'status' => 'rejected',
            'is_published' => false,
        ]);
    }
}
