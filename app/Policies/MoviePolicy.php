<?php

namespace App\Policies;

use App\Models\Movie;
use App\Models\User;

class MoviePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasSubscriptionAccess();
    }

    public function view(User $user, Movie $movie): bool
    {
        return $user->hasSubscriptionAccess();
    }
}
