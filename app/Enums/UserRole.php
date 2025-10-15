<?php

namespace App\Enums;

enum UserRole: string
{
    case User = 'user';
    case Subscriber = 'subscriber';
    case Admin = 'admin';

    public function label(): string
    {
        return match ($this) {
            self::User => 'User',
            self::Subscriber => 'Subscriber',
            self::Admin => 'Administrator',
        };
    }

    public function canImpersonate(): bool
    {
        return $this === self::Admin;
    }

    public function canBeImpersonated(): bool
    {
        return $this !== self::Admin;
    }
}
