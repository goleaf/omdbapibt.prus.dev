<?php

namespace Database\Seeders;

use App\Enums\ParserReviewAction;
use App\Models\ParserEntry;
use App\Models\ParserEntryHistory;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class ParserEntryHistorySeeder extends Seeder
{
    /**
     * Seed parser entry review history logs linked to seeded users.
     */
    public function run(): void
    {
        if (! Schema::hasTable('parser_entry_histories')
            || ! Schema::hasTable('parser_entries')
            || ! Schema::hasTable('users')) {
            return;
        }

        if (ParserEntryHistory::query()->exists()) {
            return;
        }

        if (ParserEntry::query()->doesntExist()) {
            return;
        }

        $userIds = User::query()->pluck('id');

        if ($userIds->isEmpty()) {
            return;
        }

        ParserEntry::query()
            ->orderBy('id')
            ->chunkById(200, function ($entries) use ($userIds): void {
                $entries->each(function (ParserEntry $entry) use ($userIds): void {
                    $historyCount = random_int(1, 3);

                    Collection::times($historyCount, fn () => true)->each(function () use ($entry, $userIds): void {
                        $action = collect(ParserReviewAction::cases())->random();

                        ParserEntryHistory::query()->create([
                            'parser_entry_id' => $entry->getKey(),
                            'user_id' => $userIds->random(),
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
