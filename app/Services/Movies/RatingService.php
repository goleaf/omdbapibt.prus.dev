<?php

namespace App\Services\Movies;

use App\Models\Movie;
use App\Models\Rating;
use App\Models\User;
use Illuminate\Support\Carbon;

class RatingService
{
    /**
     * Record a numeric rating for the given movie.
     */
    public function submitScore(User $user, Movie $movie, int $score): Rating
    {
        $rating = Rating::query()->firstOrNew([
            'user_id' => $user->getKey(),
            'movie_id' => $movie->getKey(),
        ]);

        $rating->rating = $score;
        $rating->rated_at = Carbon::now();

        if (! $rating->exists) {
            $rating->liked = false;
            $rating->disliked = false;
        }

        $rating->save();

        return $rating;
    }

    /**
     * Toggle the like state for the movie, clearing dislike if set.
     */
    public function toggleLike(User $user, Movie $movie): Rating
    {
        $rating = Rating::query()->firstOrNew([
            'user_id' => $user->getKey(),
            'movie_id' => $movie->getKey(),
        ]);

        $rating->liked = ! (bool) $rating->liked;

        if ($rating->liked) {
            $rating->disliked = false;
        }

        $rating->rated_at = Carbon::now();

        $rating->save();

        return $rating;
    }

    /**
     * Toggle the dislike state for the movie, clearing like if set.
     */
    public function toggleDislike(User $user, Movie $movie): Rating
    {
        $rating = Rating::query()->firstOrNew([
            'user_id' => $user->getKey(),
            'movie_id' => $movie->getKey(),
        ]);

        $rating->disliked = ! (bool) $rating->disliked;

        if ($rating->disliked) {
            $rating->liked = false;
        }

        $rating->rated_at = Carbon::now();

        $rating->save();

        return $rating;
    }

    /**
     * Locate the rating record for the authenticated user and movie.
     */
    public function findForUser(User $user, Movie $movie): ?Rating
    {
        return Rating::query()
            ->where('user_id', $user->getKey())
            ->where('movie_id', $movie->getKey())
            ->first();
    }
}
