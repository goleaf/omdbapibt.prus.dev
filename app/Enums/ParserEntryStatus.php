<?php

namespace App\Enums;

enum ParserEntryStatus: string
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';

    public function isFinalized(): bool
    {
        return match ($this) {
            self::Approved, self::Rejected => true,
            self::Pending => false,
        };
    }
}
