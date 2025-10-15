<?php

namespace App\Enums;

enum UserRole: string
{
    case GUEST = 'guest';
    case FREE_USER = 'free_user';
    case SUBSCRIBER = 'subscriber';
    case ADMIN = 'admin';

    public static function options(): array
    {
        return array_column(self::cases(), 'value');
    }
}
