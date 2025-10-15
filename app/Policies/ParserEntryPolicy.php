<?php

namespace App\Policies;

use App\Enums\ParserWorkload;
use App\Models\ParserEntry;
use App\Models\User;

class ParserEntryPolicy
{
    public function viewAny(?User $user): bool
    {
        return $this->canModerate($user);
    }

    public function view(?User $user, ParserEntry $entry): bool
    {
        return $this->canModerate($user);
    }

    public function review(?User $user, ParserEntry $entry): bool
    {
        return $this->canModerate($user);
    }

    public function trigger(?User $user, ParserWorkload $workload): bool
    {
        return $this->canModerate($user);
    }

    protected function canModerate(?User $user): bool
    {
        return $user?->isAdmin() ?? false;
    }
}
