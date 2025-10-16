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

        $target = 1_000;
        $existing = Review::query()->count();
        $remaining = max(0, $target - $existing);

        if ($remaining === 0) {
            return;
        }

        $userIds = User::query()->pluck('id');
        $movies = Movie::query()->get();

        if ($userIds->isEmpty() || $movies->isEmpty()) {
            return;
        }

        $this->forChunkedCount($remaining, 200, function (int $count) use ($userIds, $movies): void {
            Review::factory()
                ->count($count)
                ->make(['user_id' => null])
                ->each(function (Review $review) use ($userIds, $movies): void {
                    $movie = $movies->random();

                    $review->user_id = $userIds->random();
                    $review->movie_title = $movie->localizedTitle('en');

                    $review->save();
                });
        });
    }
}
