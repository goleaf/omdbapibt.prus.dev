<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function export(User $user): bool
    {
        return $user->isAdmin();
    }

    public function updateRole(User $user, User $target): bool
    {
        if (! $user->isAdmin()) {
            return false;
        }

        if ($user->is($target)) {
            return false;
        }

        return true;
    }

    public function impersonate(User $user, User $target): bool
    {
        if (! $user->isAdmin()) {
            return false;
        }

        if ($user->is($target)) {
            return false;
        }

        return $target->canBeImpersonated();
    }

    public function endImpersonation(User $user, ?User $impersonator): bool
    {
        if (! $impersonator) {
            return false;
        }

        if ($user->is($impersonator)) {
            return true;
        }

        return $impersonator->isAdmin();
    }
}
