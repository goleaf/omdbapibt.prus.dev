<?php

namespace Database\Seeders;

use App\Models\Movie;
use App\Models\TvShow;
use App\Models\User;
use App\Models\WatchHistory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class WatchHistorySeeder extends Seeder
{
    /**
     * Seed watch activity for existing users across movies and TV shows.
     */
    public function run(): void
    {
        if (! Schema::hasTable('watch_histories')
            || ! Schema::hasTable('users')
            || ! Schema::hasTable('movies')
            || ! Schema::hasTable('tv_shows')) {
            return;
        }

        if (WatchHistory::query()->exists()) {
            return;
        }

        $users = User::query()->get();
        $movies = Movie::query()->get();
        $shows = TvShow::query()->get();

        if ($users->isEmpty()) {
            return;
        }

        $users->each(function (User $user) use ($movies, $shows): void {
            if ($movies->isNotEmpty()) {
                $movieCount = min($movies->count(), random_int(1, 4));
                $movieSelection = Collection::wrap($movies->random($movieCount));

                $movieSelection->each(function (Movie $movie, int $index) use ($user): void {
                    WatchHistory::factory()
                        ->forWatchable($movie)
                        ->create([
                            'user_id' => $user->getKey(),
                            'watched_at' => now()->subDays(random_int(0, 45))->subHours($index),
                        ]);
                });
            }

            if ($shows->isNotEmpty()) {
                $showCount = min($shows->count(), random_int(0, 3));

                if ($showCount > 0) {
                    $showSelection = Collection::wrap($shows->random($showCount));

                    $showSelection->each(function (TvShow $show, int $index) use ($user): void {
                        WatchHistory::factory()
                            ->forWatchable($show)
                            ->create([
                                'user_id' => $user->getKey(),
                                'watched_at' => now()->subDays(random_int(0, 60))->subHours($index + 2),
                            ]);
                    });
                }
            }
        });
    }
}
