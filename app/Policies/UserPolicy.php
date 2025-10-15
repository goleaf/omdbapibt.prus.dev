<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    public function viewAny(?User $user): bool
    {
        return $user?->isAdmin() ?? false;
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

    public function export(User $user): bool
    {
        return $user->isAdmin();
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

    public function endImpersonation(User $user, ?User $impersonator, ?User $impersonated = null): bool
    {
        if (! $impersonator || ! $impersonated) {
            return false;
        }

        if ($user->is($impersonator)) {
            return true;
        }

        if ($user->is($impersonated)) {
            return true;
        }

        return false;
    }
}
