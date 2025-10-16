<?php

namespace Database\Seeders;

use App\Models\Movie;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    private const TOTAL_REVIEWS = 1000;

    private const CHUNK_SIZE = 200;

    /**
     * Seed community reviews tied to existing users.
     */
    public function run(): void
    {
        if (Review::query()->exists()) {
            return;
        }

        $users = User::query()->select('id')->get();
        $movies = Movie::query()->select(['id', 'title'])->get();

        if ($users->isEmpty() || $movies->isEmpty()) {
            return;
        }

        $remaining = self::TOTAL_REVIEWS;

        while ($remaining > 0) {
            $batchSize = min(self::CHUNK_SIZE, $remaining);

            Review::factory()
                ->count($batchSize)
                ->make(['user_id' => null])
                ->each(function (Review $review) use ($users, $movies): void {
                    $user = $users->random();
                    $movie = $movies->random();

                    $review->user_id = $user->getKey();
                    $review->movie_title = is_array($movie->title)
                        ? ($movie->title['en'] ?? collect($movie->title)->first())
                        : (string) $movie->title;

                    $review->save();
                });

            $remaining -= $batchSize;
        }
    }
}
