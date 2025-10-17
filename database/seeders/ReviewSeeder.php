<?php

namespace Database\Seeders;

use App\Models\Movie;
use App\Models\Review;
use App\Models\User;
use Database\Seeders\Concerns\HandlesSeederChunks;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class ReviewSeeder extends Seeder
{
    use HandlesSeederChunks;

    /**
     * Seed community reviews tied to existing users.
     */
    public function run(): void
    {
        if (! Schema::hasTable('reviews')
            || ! Schema::hasTable('users')
            || ! Schema::hasTable('movies')) {
            return;
        }

        if (Review::query()->exists()) {
            return;
        }

        $userIds = User::query()->pluck('id');
        $movies = Movie::query()->get();

        if ($userIds->isEmpty() || $movies->isEmpty()) {
            return;
        }

        $this->forChunkedCount(1_000, 200, function (int $count) use ($userIds, $movies): void {
            Review::factory()
                ->count($count)
                ->make(['user_id' => null, 'movie_id' => null])
                ->each(function (Review $review) use ($userIds, $movies): void {
                    $movie = $movies->random();

                    $review->user_id = $userIds->random();
                    $review->movie_id = $movie->id;

                    $review->save();
                });
        });
    }
}
