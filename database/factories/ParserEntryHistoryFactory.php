<?php

namespace Database\Factories;

use App\Enums\ParserReviewAction;
use App\Models\ParserEntry;
use App\Models\ParserEntryHistory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ParserEntryHistory>
 */
class ParserEntryHistoryFactory extends Factory
{
    protected $model = ParserEntryHistory::class;

    public function definition(): array
    {
        return [
            'parser_entry_id' => ParserEntry::factory(),
            'user_id' => User::factory(),
            'action' => $this->faker->randomElement(ParserReviewAction::cases())->value,
            'changes' => [
                [
                    'key' => 'title',
                    'before' => $this->faker->sentence(3),
                    'after' => $this->faker->sentence(3),
                ],
            ],
            'notes' => $this->faker->optional()->sentence(),
        ];
    }
}
