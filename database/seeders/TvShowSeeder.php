<?php

namespace Database\Seeders;

use App\Models\Person;
use App\Models\TvShow;
use App\Models\User;
use Database\Seeders\Concerns\HandlesSeederChunks;
use Illuminate\Database\Seeder;

class TvShowSeeder extends Seeder
{
    use HandlesSeederChunks;

    /**
     * Seed scripted series alongside credit and watchlist relationships.
     */
    public function run(): void
    {
        if (TvShow::query()->exists()) {
            return;
        }

        $personIds = Person::query()->pluck('id');
        $userIds = User::query()->pluck('id');

        $this->forChunkedCount(1_000, 100, function (int $count) use ($personIds, $userIds): void {
            TvShow::factory()
                ->count($count)
                ->create()
                ->each(function (TvShow $show) use ($personIds, $userIds): void {
                    if ($personIds->isNotEmpty()) {
                        $creditCount = min($personIds->count(), random_int(4, 10));
                        $creditSelection = collect($personIds->random($creditCount))->values();

                        $show->people()->syncWithoutDetaching(
                            $creditSelection->mapWithKeys(function (int $personId, int $index): array {
                                $isCast = $index < 4;

                                return [
                                    $personId => [
                                        'credit_type' => $isCast ? 'cast' : 'crew',
                                        'department' => $isCast ? 'Acting' : fake()->randomElement(['Directing', 'Production', 'Writing']),
                                        'character' => $isCast ? fake()->name() : null,
                                        'job' => $isCast ? null : fake()->randomElement(['Showrunner', 'Producer', 'Writer']),
                                        'credit_order' => $index + 1,
                                    ],
                                ];
                            })->all()
                        );
                    }

                    if ($userIds->isNotEmpty()) {
                        $watchlistCount = min($userIds->count(), random_int(0, 5));

                        if ($watchlistCount > 0) {
                            $selectedUsers = collect($userIds->random($watchlistCount))->values()->all();
                            $show->watchlistedBy()->syncWithoutDetaching($selectedUsers);
                        }
                    }
                });
        });
    }
}
