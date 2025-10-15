<?php

namespace App\Enums;

enum SubscriptionStatus: string
{
    case Active = 'active';
    case Trialing = 'trialing';
    case PastDue = 'past_due';
    case Incomplete = 'incomplete';
    case IncompleteExpired = 'incomplete_expired';
    case Unpaid = 'unpaid';
    case Canceled = 'canceled';

    /**
     * Return the statuses that should be considered "active" for analytics and access checks.
     *
     * @return array<int, self>
     */
    public static function active(): array
    {
        return [
            self::Active,
            self::Trialing,
            self::PastDue,
            self::Incomplete,
            self::IncompleteExpired,
            self::Unpaid,
        ];
    }

    /**
     * Convert the given statuses into their string values for database queries or config arrays.
     *
     * @param  array<int, self|string>  $statuses
     * @return array<int, string>
     */
    public static function values(array $statuses): array
    {
        return array_values(array_map(
            static fn (self|string $status): string => $status instanceof self
                ? $status->value
                : strtolower((string) $status),
            $statuses
        ));
    }

    /**
     * Retrieve the string values of the default active statuses.
     *
     * @return array<int, string>
     */
    public static function activeValues(): array
    {
        return self::values(self::active());
    }

    public static function tryFromValue(mixed $status): ?self
    {
        if ($status instanceof self) {
            return $status;
        }

        if (is_string($status)) {
            return self::tryFrom(strtolower($status));
        }

        return null;
    }

    public function isActive(): bool
    {
        return in_array($this, self::active(), true);
    }
}
