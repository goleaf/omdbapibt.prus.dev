<?php

namespace Database\Seeders;

use App\Enums\ParserEntryStatus;
use App\Enums\UserRole;
use App\Models\Movie;
use App\Models\ParserEntry;
use App\Models\User;
use Database\Seeders\Concerns\SeedsModelsInChunks;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class ParserEntrySeeder extends Seeder
{
    use SeedsModelsInChunks;

    private const CHUNK_SIZE = 100;

    /**
     * Seed parser entries for existing media that simulate ingestion runs.
     */
    public function run(): void
    {
        if (ParserEntry::query()->exists()) {
            return;
        }

        if (! Movie::query()->exists()) {
            return;
        }

        $reviewers = User::query()
            ->where('role', UserRole::Admin->value)
            ->get();

        Movie::query()
            ->orderBy('id')
            ->chunkById(self::CHUNK_SIZE, function (Collection $movies) use ($reviewers): void {
                $payloads = Collection::make();

                $movies->each(function (Movie $movie) use (&$payloads, $reviewers): void {
                    $entryTotal = random_int(1, 2);

                    $entries = ParserEntry::factory()
                        ->count($entryTotal)
                        ->state(function () use ($movie, $reviewers): array {
                            $status = collect(ParserEntryStatus::cases())->random();

                            $reviewerId = null;
                            $reviewedAt = null;

                            if ($status->isFinalized() && $reviewers->isNotEmpty()) {
                                $reviewer = $reviewers->random();
                                $reviewerId = $reviewer->getKey();
                                $reviewedAt = now()->subDays(random_int(0, 14));
                            }

                            return [
                                'subject_type' => Movie::class,
                                'subject_id' => $movie->getKey(),
                                'status' => $status->value,
                                'reviewed_by' => $reviewerId,
                                'reviewed_at' => $reviewedAt,
                            ];
                        })
                        ->make()
                        ->map(fn (ParserEntry $entry): array => array_merge($entry->getAttributes(), [
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]));

                    $payloads = $payloads->merge($entries);
                });

                if ($payloads->isNotEmpty()) {
                    $normalized = $payloads->map(function (array $attributes): array {
                        $attributes['payload'] = json_encode($attributes['payload']);
                        $attributes['baseline_snapshot'] = json_encode($attributes['baseline_snapshot']);

                        return $attributes;
                    });

                    $this->chunkedInsert($normalized, 500, static fn (array $chunk): bool => ParserEntry::query()->insert($chunk));
                }
            });
    }
}
