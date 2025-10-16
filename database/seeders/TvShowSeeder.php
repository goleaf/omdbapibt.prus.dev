<?php

namespace Database\Seeders;

use App\Models\Person;
use App\Models\TvShow;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class TvShowSeeder extends Seeder
{
    /**
     * Seed scripted series alongside credit and watchlist relationships.
     */
    public function run(): void
    {
        if (TvShow::query()->exists()) {
            return;
        }

        $people = Person::query()->get();
        $users = User::query()->get();

        TvShow::factory()
            ->count(1000)
            ->create()
            ->each(function (TvShow $show) use ($people, $users): void {
                if ($people->isNotEmpty()) {
                    $creditPool = Collection::wrap($people->random(min($people->count(), random_int(4, 10))));

                    $show->people()->syncWithoutDetaching(
                        $creditPool->values()->mapWithKeys(function (Person $person, int $index): array {
                            $isCast = $index < 4;

                            return [
                                $person->getKey() => [
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

                if ($users->isNotEmpty()) {
                    $watchlistCount = min($users->count(), random_int(0, 5));

                    if ($watchlistCount > 0) {
                        $userIds = Collection::wrap($users->random($watchlistCount))->pluck('id')->all();
                        $show->watchlistedBy()->syncWithoutDetaching($userIds);
                    }
                }
            });
    }
}
