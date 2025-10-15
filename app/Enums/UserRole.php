<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case Moderator = 'moderator';
    case Support = 'support';
    case User = 'user';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Administrator',
            self::Moderator => 'Moderator',
            self::Support => 'Support Agent',
            self::User => 'Member',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::Admin => 'Full access to administrative tooling and billing controls.',
            self::Moderator => 'Can moderate community content and view user profiles.',
            self::Support => 'Handles user requests with limited administrative capabilities.',
            self::User => 'Standard subscriber with personal account access.',
        };
    }

    public static function selectable(): array
    {
        return collect(self::cases())
            ->reject(fn (self $role) => $role === self::Admin)
            ->mapWithKeys(fn (self $role) => [
                $role->value => [
                    'label' => $role->label(),
                    'description' => $role->description(),
                ],
            ])
            ->all();
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $role) => [
                $role->value => [
                    'label' => $role->label(),
                    'description' => $role->description(),
                ],
            ])
            ->all();
    }
}
