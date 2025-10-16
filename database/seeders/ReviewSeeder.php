<?php

namespace Database\Seeders;

use App\Models\Movie;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class ReviewSeeder extends Seeder
{
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

        $users = User::query()->get();
        $movies = Movie::query()->get();

        if ($users->isEmpty() || $movies->isEmpty()) {
            return;
        }

        Review::factory()
            ->count(30)
            ->make()
            ->each(function (Review $review) use ($users, $movies): void {
                $user = $users->random();
                $movie = $movies->random();

                $review->user_id = $user->getKey();
                $review->movie_title = is_array($movie->title)
                    ? ($movie->title['en'] ?? collect($movie->title)->first())
                    : (string) $movie->title;

                $review->save();
            });
    }
}
