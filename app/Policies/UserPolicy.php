<?php

namespace App\Policies;

use App\Models\User;
use App\Support\ImpersonationManager;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    public function viewAny(?User $user): bool
    {
        return $user?->isAdmin() ?? false;
    }

    public function updateRole(?User $user, User $target): bool
    {
        if (! $user?->isAdmin()) {
            return false;
        }

        return true;
    }

    public function export(?User $user): bool
    {
        return $user?->isAdmin() ?? false;
    }

    public function impersonate(?User $user, User $target): bool
    {
        if (! $user?->isAdmin()) {
            return false;
        }

        return $target->canBeImpersonated();
    }

    public function stopImpersonating(?User $user): bool
    {
        $manager = app(ImpersonationManager::class);

        if (! $manager->isImpersonating()) {
            return $user?->isAdmin() ?? false;
        }

        $impersonator = $manager->impersonator();

        if (! $impersonator) {
            return false;
        }

        return $impersonator->isAdmin();
    }
}
