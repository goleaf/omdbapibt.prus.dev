<?php

namespace App\Policies;

use App\Models\UiTranslation;
use App\Models\User;

class UiTranslationPolicy
{
    public function view(?User $user, UiTranslation|string|null $translation = null): bool
    {
        return $user?->isAdmin() ?? false;
    }

    public function create(?User $user): bool
    {
        return $user?->isAdmin() ?? false;
    }

    public function update(?User $user, UiTranslation|string|null $translation = null): bool
    {
        return $user?->isAdmin() ?? false;
    }

    public function delete(?User $user, UiTranslation|string|null $translation = null): bool
    {
        return $user?->isAdmin() ?? false;
    }
}
