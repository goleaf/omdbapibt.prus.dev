<?php

namespace App\Policies;

use App\Models\User;

class AdminCrudPolicy
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

    public function view(?User $user): bool
    {
        return false;
    }

    public function create(?User $user): bool
    {
        return false;
    }

    public function update(?User $user): bool
    {
        return false;
    }

    public function delete(?User $user): bool
    {
        return false;
    }

    public function restore(?User $user): bool
    {
        return false;
    }

    public function forceDelete(?User $user): bool
    {
        return false;
    }
}
