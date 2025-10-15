<?php

declare(strict_types=1);

namespace App\Support;

use Illuminate\Support\Str;

class TmdbImage
{
    public static function profile(?string $path): string
    {
        return self::make($path, config('services.tmdb.images.profiles.size'), config('services.tmdb.placeholders.profile'));
    }

    public static function poster(?string $path): string
    {
        return self::make($path, config('services.tmdb.images.posters.size'), config('services.tmdb.placeholders.poster'));
    }

    protected static function make(?string $path, ?string $size, ?string $placeholder): string
    {
        if (blank($path)) {
            return asset($placeholder ?? '');
        }

        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        $baseUrl = rtrim(config('services.tmdb.images.base_url'), '/');
        $sizeSegment = trim((string) $size, '/');
        $imagePath = ltrim($path, '/');

        return sprintf('%s/%s/%s', $baseUrl, $sizeSegment, $imagePath);
    }
}
