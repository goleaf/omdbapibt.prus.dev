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
        /** @var Movie $model */
        $model = $this->upsertModel(Movie::query(), $payload);

        return $model;
    }

    /**
     * Upsert a TV show record using the provided payload.
     */
    public function upsertTvShow(array $payload): TvShow
    {
        /** @var TvShow $model */
        $model = $this->upsertModel(TvShow::query(), $payload);

        return $model;
    }

    /**
     * Perform the upsert operation and gracefully handle uniqueness conflicts.
     */
    protected function upsertModel(Builder $query, array $payload): Model
    {
        $attributes = $this->prepareAttributes($payload);
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
}
