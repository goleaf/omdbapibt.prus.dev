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
    private const CHUNK_SIZE = 250;

    /**
     * Seed parser entry review history logs linked to seeded users.
     */
    public function run(): void
    {
        if (ParserEntryHistory::query()->exists()) {
            return;
        }

        $users = User::query()->select('id')->get();

        if ($users->isEmpty()) {
            return;
        }

        if (! ParserEntry::query()->exists()) {
            return;
        }

        ParserEntry::query()
            ->orderBy('id')
            ->chunkById(self::CHUNK_SIZE, function (Collection $entries) use ($users): void {
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
            });
    }
}
