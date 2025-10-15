<?php

namespace App\Policies;

use App\Models\ParserEntry;
use App\Models\User;

class ParserEntryPolicy
{
    public function trigger(?User $user): bool
    {
        return $user?->isAdmin() ?? false;
    }

    public function review(?User $user): bool
    {
        return $user?->isAdmin() ?? false;
    }

    public function update(?User $user, ParserEntry $entry): bool
    {
        return $user?->isAdmin() ?? false;
    }
}
