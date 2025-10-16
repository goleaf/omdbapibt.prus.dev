<?php

namespace App\Services\Parser;

use App\Models\Movie;
use App\Models\TvShow;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MediaUpsertService
{
    /**
     * Upsert a movie record using the provided payload.
     */
    public function upsertMovie(array $payload): Movie
    {
        $attributes = $this->prepareAttributes($payload);

        /** @var Movie $model */
        $model = $this->upsertModel(Movie::query(), $attributes);

        return $model;
    }

    /**
     * Upsert a TV show record using the provided payload.
     */
    public function upsertTvShow(array $payload): TvShow
    {
        $attributes = $this->prepareAttributes($payload);
        $attributes = $this->prepareTvShowTranslations($attributes);

        /** @var TvShow $model */
        $model = $this->upsertModel(TvShow::query(), $attributes);

        return $model;
    }

    /**
     * Perform the upsert operation and gracefully handle uniqueness conflicts.
     */
    protected function upsertModel(Builder $query, array $attributes): Model
    {
        $modelClass = $query->getModel()::class;

        return DB::transaction(function () use ($query, $attributes, $modelClass) {
            if ($existing = $this->findExistingRecord($query, $attributes)) {
                $existing->fill($attributes);
                $existing->save();

                return $existing->refresh();
            }

            $model = new $modelClass($attributes);

            try {
                $model->save();

                return $model->refresh();
            } catch (QueryException $exception) {
                if (! $this->isUniqueConstraintViolation($exception)) {
                    throw $exception;
                }

                if ($existing = $this->findExistingRecord($query, $attributes, true)) {
                    $existing->fill($attributes);
                    $existing->save();

                    return $existing->refresh();
                }

                throw $exception;
            }
        });
    }

    /**
     * Prepare attributes by calculating the deduplication hash when possible.
     */
    protected function prepareAttributes(array $payload): array
    {
        $attributes = $payload;
        $attributes['dedup_hash'] = $this->computeDedupHash(
            Arr::only($payload, ['tmdb_id', 'imdb_id'])
        );

        return $attributes;
    }

    /**
     * Normalise translated payload attributes for TV shows.
     */
    protected function prepareTvShowTranslations(array $attributes): array
    {
        $attributes = $this->synchroniseTranslationPair($attributes, 'name', 'name_translations');
        $attributes = $this->synchroniseTranslationPair($attributes, 'overview', 'overview_translations');
        $attributes = $this->synchroniseTranslationPair($attributes, 'tagline', 'tagline_translations');

        return $attributes;
    }

    /**
     * Calculate a stable deduplication hash for the provided identifier values.
     */
    protected function computeDedupHash(array $identifiers): ?string
    {
        $parts = [];

        foreach ($identifiers as $key => $value) {
            if (is_null($value) || $value === '') {
                continue;
            }

            $parts[] = sprintf('%s:%s', $key, trim((string) $value));
        }

        if (empty($parts)) {
            return null;
        }

        sort($parts);

        return md5(implode('|', $parts));
    }

    /**
     * Attempt to find an existing record matching the provided attributes.
     */
    protected function findExistingRecord(Builder $query, array $attributes, bool $forceReload = false): ?Model
    {
        $candidates = [];

        if (! empty($attributes['dedup_hash'])) {
            $candidates[] = ['dedup_hash', '=', $attributes['dedup_hash']];
        }

        foreach (['tmdb_id', 'imdb_id', 'slug'] as $column) {
            if (! empty($attributes[$column])) {
                $candidates[] = [$column, '=', $attributes[$column]];
            }
        }

        foreach ($candidates as $candidate) {
            [$column, $operator, $value] = $candidate;
            $existing = (clone $query)
                ->where($column, $operator, $value)
                ->when($forceReload, fn (Builder $builder) => $builder->lockForUpdate())
                ->first();

            if ($existing) {
                return $existing;
            }
        }

        return null;
    }

    /**
     * Determine whether the exception was caused by a unique constraint violation.
     */
    protected function isUniqueConstraintViolation(QueryException $exception): bool
    {
        $sqlState = $exception->errorInfo[0] ?? null;

        if ($sqlState && Str::startsWith($sqlState, '23')) {
            return true;
        }

        return Str::contains(Str::lower($exception->getMessage()), 'unique');
    }

    protected function synchroniseTranslationPair(array $attributes, string $baseKey, string $translationsKey): array
    {
        $translations = $attributes[$translationsKey] ?? null;

        if (is_string($translations)) {
            $decoded = json_decode($translations, true);
            $translations = is_array($decoded) ? $decoded : ['en' => $translations];
        }

        if (! is_array($translations)) {
            $translations = [];
        }

        $translations = array_filter($translations, function ($value) {
            return is_string($value) && trim($value) !== '';
        });

        $baseValue = $attributes[$baseKey] ?? null;

        if (is_string($baseValue) && trim($baseValue) !== '') {
            $translations['en'] ??= $baseValue;
        }

        if (! isset($attributes[$baseKey]) || ! is_string($attributes[$baseKey]) || trim($attributes[$baseKey]) === '') {
            $primary = $this->determinePrimaryTranslation($translations);

            if ($primary !== null) {
                $attributes[$baseKey] = $primary;
            }
        }

        if (! empty($translations)) {
            $attributes[$translationsKey] = $translations;
        } else {
            $attributes[$translationsKey] = null;
        }

        return $attributes;
    }

    protected function determinePrimaryTranslation(array $translations): ?string
    {
        $locale = app()->getLocale();

        if ($locale && isset($translations[$locale])) {
            return $translations[$locale];
        }

        $fallbackLocale = config('app.fallback_locale');

        if ($fallbackLocale && isset($translations[$fallbackLocale])) {
            return $translations[$fallbackLocale];
        }

        if (isset($translations['en'])) {
            return $translations['en'];
        }

        foreach ($translations as $value) {
            if (is_string($value) && trim($value) !== '') {
                return $value;
            }
        }

        return null;
    }
}
