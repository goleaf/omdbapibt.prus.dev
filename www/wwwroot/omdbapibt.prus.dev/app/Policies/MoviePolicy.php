<?php

namespace App\Policies;

use App\Models\Movie;
use App\Models\User;

class MoviePolicy
{
    /**
     * Determine whether the user can view a movie.
     */
    public function view(?User $user, Movie $movie): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view premium details for a movie.
     */
    public function viewPremium(?User $user, Movie $movie): bool
    {
        if (! $user) {
            return false;
        }

        return $user->subscribed('default') || $user->onTrial();
    }
}
