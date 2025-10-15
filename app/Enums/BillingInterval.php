<?php

namespace App\Enums;

enum BillingInterval: string
{
    case Day = 'day';
    case Week = 'week';
    case Month = 'month';
    case Quarter = 'quarter';
    case Year = 'year';

    /**
     * Convert a set of intervals to their string values.
     *
     * @param  array<int, self|string>  $intervals
     * @return array<int, string>
     */
    public static function values(array $intervals): array
    {
        return array_values(array_map(
            static fn (self|string $interval): string => $interval instanceof self
                ? $interval->value
                : strtolower((string) $interval),
            $intervals
        ));
    }

    public static function tryFromValue(mixed $interval): ?self
    {
        if ($interval instanceof self) {
            return $interval;
        }

        if (is_string($interval)) {
            return self::tryFrom(strtolower($interval));
        }

        return null;
    }

    public function monthsEquivalent(): float
    {
        return match ($this) {
            self::Year => 12.0,
            self::Quarter => 3.0,
            self::Week => 0.25,
            self::Day => 1 / 30,
            self::Month => 1.0,
        };
    }
}
