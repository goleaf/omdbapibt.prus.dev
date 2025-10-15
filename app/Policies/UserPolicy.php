<?php

namespace App\Policies;

use App\Models\User;
use App\Support\ImpersonationManager;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    public function __construct(private readonly ImpersonationManager $impersonationManager) {}

    public function viewAny(User $actor): bool
    {
        return $actor->isAdmin();
    }

    public function updateRole(User $actor, User $target): bool
    {
        return $actor->isAdmin();
    }

    public function impersonate(User $actor, User $target): bool
    {
        if ($actor->is($target)) {
            return false;
        }

        return $actor->canImpersonate() && $target->canBeImpersonated();
    }

    public function stopImpersonating(User $actor): bool
    {
        if ($actor->canImpersonate()) {
            return true;
        }

        return session()->has(ImpersonationManager::SESSION_KEY);
    }
}
