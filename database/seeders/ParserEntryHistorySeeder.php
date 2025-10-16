<?php

namespace Database\Seeders;

use App\Enums\ParserReviewAction;
use App\Models\ParserEntry;
use App\Models\ParserEntryHistory;
use App\Models\User;
use Database\Seeders\Concerns\SeedsModelsInChunks;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class ParserEntryHistorySeeder extends Seeder
{
    use SeedsModelsInChunks;

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
                $payloads = Collection::make();

                $entries->each(function (ParserEntry $entry) use (&$payloads, $users): void {
                    $historyCount = random_int(1, 3);

                    Collection::times($historyCount, fn () => true)->each(function () use (&$payloads, $entry, $users): void {
                        $actor = $users->random();
                        $action = collect(ParserReviewAction::cases())->random();

                        $payloads->push([
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
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    });
                });

                if ($payloads->isNotEmpty()) {
                    $normalized = $payloads->map(function (array $attributes): array {
                        $attributes['changes'] = json_encode($attributes['changes']);

                        return $attributes;
                    });

                    $this->chunkedInsert($normalized, 500, static fn (array $chunk): bool => ParserEntryHistory::query()->insert($chunk));
                }
            });
    }
}
