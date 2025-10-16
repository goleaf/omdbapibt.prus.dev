<?php

namespace Database\Seeders;

use App\Models\Movie;
use App\Models\TvShow;
use App\Models\User;
use App\Models\WatchHistory;
use Database\Seeders\Concerns\SeedsModelsInChunks;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class WatchHistorySeeder extends Seeder
{
    use SeedsModelsInChunks;

    private const CHUNK_SIZE = 100;

    private const INSERT_CHUNK_SIZE = 500;

    /**
     * Seed watch activity for existing users across movies and TV shows.
     */
    public function run(): void
    {
        if (WatchHistory::query()->exists()) {
            return;
        }

        $movieIds = Movie::query()->pluck('id')->all();
        $showIds = TvShow::query()->pluck('id')->all();

        if (! User::query()->exists()) {
            return;
        }

        User::query()
            ->orderBy('id')
            ->chunkById(self::CHUNK_SIZE, function (Collection $users) use ($movieIds, $showIds): void {
                $records = Collection::make();

                $users->each(function (User $user) use (&$records, $movieIds, $showIds): void {
                    if ($movieIds !== []) {
                        $movieCount = min(count($movieIds), random_int(1, 4));
                        $selectedMovies = Arr::wrap(Arr::random($movieIds, $movieCount));

                        foreach ($selectedMovies as $index => $movieId) {
                            $records->push([
                                'user_id' => $user->getKey(),
                                'watchable_type' => Movie::class,
                                'watchable_id' => $movieId,
                                'watched_at' => now()->subDays(random_int(0, 45))->subHours($index),
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        }
                    }

                    if ($showIds !== []) {
                        $showCount = min(count($showIds), random_int(0, 3));

                        if ($showCount > 0) {
                            $selectedShows = Arr::wrap(Arr::random($showIds, $showCount));

                            foreach ($selectedShows as $index => $showId) {
                                $records->push([
                                    'user_id' => $user->getKey(),
                                    'watchable_type' => TvShow::class,
                                    'watchable_id' => $showId,
                                    'watched_at' => now()->subDays(random_int(0, 60))->subHours($index + 2),
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ]);
                            }
                        }
                    }
                });

                if ($records->isNotEmpty()) {
                    $this->chunkedInsert($records, self::INSERT_CHUNK_SIZE, static fn (array $chunk): bool => WatchHistory::query()->insert($chunk));
                }
            });
    }
}
