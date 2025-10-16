<?php

namespace Database\Seeders;

use App\Enums\ParserEntryStatus;
use App\Enums\UserRole;
use App\Models\Movie;
use App\Models\ParserEntry;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class ParserEntrySeeder extends Seeder
{
    /**
     * Seed parser entries for existing media that simulate ingestion runs.
     */
    public function run(): void
    {
        if (ParserEntry::query()->exists()) {
            return;
        }

        $movies = Movie::query()->get();

        if ($movies->isEmpty()) {
            return;
        }

        $reviewers = User::query()
            ->where('role', UserRole::Admin->value)
            ->get();

        $movies->each(function (Movie $movie) use ($reviewers): void {
            $entryTotal = random_int(1, 2);

            Collection::times($entryTotal, fn () => true)->each(function () use ($movie, $reviewers): void {
                $status = collect(ParserEntryStatus::cases())->random();

                $reviewerId = null;
                $reviewedAt = null;

                if ($status->isFinalized() && $reviewers->isNotEmpty()) {
                    $reviewer = $reviewers->random();
                    $reviewerId = $reviewer->getKey();
                    $reviewedAt = now()->subDays(random_int(0, 14));
                }

                ParserEntry::factory()
                    ->state([
                        'subject_type' => Movie::class,
                        'subject_id' => $movie->getKey(),
                        'status' => $status->value,
                        'reviewed_by' => $reviewerId,
                        'reviewed_at' => $reviewedAt,
                    ])
                    ->create();
            });
        });
    }
}
