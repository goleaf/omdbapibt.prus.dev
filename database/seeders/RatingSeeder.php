<?php

namespace Database\Seeders;

use App\Models\Movie;
use App\Models\Rating;
use App\Models\User;
use Database\Seeders\Concerns\HandlesSeederChunks;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class RatingSeeder extends Seeder
{
    use HandlesSeederChunks;

    /**
     * Seed user ratings and reactions for catalog movies.
     */
    public function run(): void
    {
        if (! Schema::hasTable('ratings')
            || ! Schema::hasTable('users')
            || ! Schema::hasTable('movies')) {
            return;
        }

        if (Rating::query()->exists()) {
            return;
        }

        $userIds = User::query()->pluck('id');
        $movieIds = Movie::query()->pluck('id');

        if ($userIds->isEmpty() || $movieIds->isEmpty()) {
            return;
        }

        $this->forChunkedCount(500, 100, function (int $count) use ($userIds, $movieIds): void {
            Rating::factory()
                ->count($count)
                ->make(['user_id' => null, 'movie_id' => null])
                ->each(function (Rating $rating) use ($userIds, $movieIds): void {
                    $userId = $userIds->random();
                    $movieId = $movieIds->random();

                    Rating::query()->updateOrCreate([
                        'user_id' => $userId,
                        'movie_id' => $movieId,
                    ], [
                        'rating' => $rating->rating,
                        'liked' => $rating->liked,
                        'disliked' => $rating->disliked,
                        'rated_at' => $rating->rated_at ?? now(),
                    ]);
                });
        });
    }
}
