<?php

namespace App\Support;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use JsonException;

class TvShowRepository
{
    protected Collection $shows;

    public function __construct(?string $path = null)
    {
        $path ??= database_path('data/tv_shows.json');

        if (! File::exists($path)) {
            throw new ModelNotFoundException("TV show data file not found at {$path}.");
        }

        try {
            $decoded = json_decode(File::get($path), true, flags: JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw new JsonException('Unable to decode TV show dataset: '.$exception->getMessage(), previous: $exception);
        }

        $this->shows = collect($decoded);
    }

    public function findBySlugOrId(string $identifier): array
    {
        $show = $this->shows->first(function (array $show) use ($identifier): bool {
            return (string) ($show['slug'] ?? '') === $identifier
                || (string) ($show['id'] ?? '') === $identifier;
        });

        if (! $show) {
            throw (new ModelNotFoundException('TV show not found.'));
        }

        return $show;
    }

    public function seasonsFor(array $show): array
    {
        return $show['seasons'] ?? [];
    }

    public function creditsFor(array $show): array
    {
        $credits = $show['credits'] ?? [];

        return [
            'cast' => $credits['cast'] ?? [],
            'crew' => $credits['crew'] ?? [],
        ];
    }
}
