<?php

namespace App\Policies;

use App\Models\Movie;
use App\Models\User;

class MoviePolicy
{
    public function view(?User $user, Movie $movie): bool
    {
        if (! $movie->requiresSubscription()) {
            return true;
        }

        if (! $user instanceof User) {
            return false;
        }

        return $user->hasPremiumAccess();
    }
}
