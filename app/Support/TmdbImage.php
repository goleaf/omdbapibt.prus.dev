<?php

namespace App\Support;

class TmdbImage
{
    public static function profile(?string $path, string $size = 'h632'): string
    {
        return self::buildUrl($path, config('tmdb.profile_sizes'), $size, 'person');
    }

    public static function poster(?string $path, string $size = 'w500'): string
    {
        return self::buildUrl($path, config('tmdb.poster_sizes'), $size, 'poster');
    }

    protected static function buildUrl(?string $path, array $availableSizes, string $preferred, string $placeholder): string
    {
        if (empty($path)) {
            return asset("images/placeholders/{$placeholder}.svg");
        }

        $size = in_array($preferred, $availableSizes, true)
            ? $preferred
            : ($availableSizes[0] ?? 'original');

        $base = rtrim((string) config('tmdb.image_base'), '/');

        return sprintf('%s/%s%s', $base, $size, $path);
    }
}
