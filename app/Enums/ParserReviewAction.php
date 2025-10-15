<?php

namespace App\Enums;

enum ParserReviewAction: string
{
    case Queued = 'queued';
    case Approved = 'approved';
    case Rejected = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::Queued => 'Queued',
            self::Approved => 'Approved',
            self::Rejected => 'Rejected',
        };
    }
}
