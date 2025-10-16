<?php

namespace Database\Seeders;

use App\Enums\ParserReviewAction;
use App\Models\ParserEntry;
use App\Models\ParserEntryHistory;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class ParserEntryHistorySeeder extends Seeder
{
    /**
     * Seed parser entry review history logs linked to seeded users.
     */
    public function run(): void
    {
        if (ParserEntryHistory::query()->exists()) {
            return;
        }

        $entries = ParserEntry::query()->get();
        $users = User::query()->get();

        if ($entries->isEmpty() || $users->isEmpty()) {
            return;
        }

        $entries->each(function (ParserEntry $entry) use ($users): void {
            $historyCount = random_int(1, 3);

            Collection::times($historyCount, fn () => true)->each(function () use ($entry, $users): void {
                $actor = $users->random();
                $action = collect(ParserReviewAction::cases())->random();

                ParserEntryHistory::query()->create([
                    'parser_entry_id' => $entry->getKey(),
                    'user_id' => $actor->getKey(),
                    'action' => $action->value,
                    'changes' => [
                        [
                            'key' => 'popularity',
                            'before' => fake()->randomFloat(2, 0, 500),
                            'after' => fake()->randomFloat(2, 0, 500),
                        ],
                    ],
                    'notes' => fake()->boolean(50) ? fake()->sentence() : null,
                ]);
            });
        });
    }
}
