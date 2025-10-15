<?php

namespace App\Enums;

enum ParserWorkload: string
{
    case Movies = 'movies';
    case Tv = 'tv';
    case People = 'people';

    /**
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_map(static fn (self $workload): string => $workload->value, self::cases());
    }

    public static function isValid(string $value): bool
    {
        return in_array($value, self::values(), true);
    }
}
