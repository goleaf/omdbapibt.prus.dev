<?php

namespace Database\Factories;

use App\Models\ParsedEntry;
use App\Models\ParsedEntryHistory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ParsedEntryHistory>
 */
class ParsedEntryHistoryFactory extends Factory
{
    protected $model = ParsedEntryHistory::class;

    public function definition(): array
    {
        return [
            'parsed_entry_id' => ParsedEntry::factory(),
            'user_id' => User::factory(),
            'action' => $this->faker->randomElement(['comment', 'approved', 'rejected']),
            'notes' => $this->faker->sentence(),
        ];
    }
}
