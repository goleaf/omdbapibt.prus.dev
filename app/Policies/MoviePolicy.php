<?php

namespace App\Policies;

use App\Models\Movie;
use App\Models\User;

class MoviePolicy
{
    public function before(?User $user): ?bool
    {
        if ($user && $user->isAdmin()) {
            return true;
        }

        return null;
    }

    public function viewAny(?User $user): bool
    {
        return false;
    }

    public function create(?User $user): bool
    {
        return false;
    }

    public function update(?User $user, Movie $movie): bool
    {
        return false;
    }

    public function delete(?User $user, Movie $movie): bool
    {
        return false;
    }

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

    public function restore(?User $user, Movie $movie): bool
    {
        return false;
    }

    public function forceDelete(?User $user, Movie $movie): bool
    {
        return false;
    }
}
