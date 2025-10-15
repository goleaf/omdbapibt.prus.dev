<?php

namespace App\Policies;

use App\Models\UiTranslation;
use App\Models\User;

class UiTranslationPolicy
{
    public function viewAny(?User $user): bool
    {
        return $user?->isAdmin() ?? false;
    }

    public function create(?User $user): bool
    {
        return $this->viewAny($user);
    }

    public function update(?User $user, UiTranslation $translation): bool
    {
        return $this->viewAny($user);
    }

    public function delete(?User $user, UiTranslation $translation): bool
    {
        return $this->viewAny($user);
    }
}
