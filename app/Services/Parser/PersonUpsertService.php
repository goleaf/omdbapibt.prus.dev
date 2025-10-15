<?php

namespace App\Services\Parser;

use App\Models\Person;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\QueryException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class PersonUpsertService
{
    public function upsertPerson(array $payload): Person
    {
        $attributes = Arr::only($payload, (new Person)->getFillable());

        return DB::transaction(function () use ($attributes) {
            $query = Person::query();

            if ($existing = $this->findExistingRecord($query, $attributes)) {
                $existing->fill($attributes);
                $existing->save();

                return $existing->refresh();
            }

            $person = new Person($attributes);

            try {
                $person->save();

                return $person->refresh();
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

    protected function findExistingRecord(Builder $query, array $attributes, bool $lock = false): ?Person
    {
        foreach (['tmdb_id', 'imdb_id', 'slug'] as $column) {
            $value = $attributes[$column] ?? null;

            if (empty($value)) {
                continue;
            }

            $builder = (clone $query)->where($column, $value);

            if ($lock) {
                $builder->lockForUpdate();
            }

            /** @var Person|null $existing */
            $existing = $builder->first();

            if ($existing) {
                return $existing;
            }
        }

        return null;
    }

    protected function isUniqueConstraintViolation(QueryException $exception): bool
    {
        $sqlState = $exception->errorInfo[0] ?? null;

        if ($sqlState && str_starts_with((string) $sqlState, '23')) {
            return true;
        }

        return str_contains(strtolower($exception->getMessage()), 'unique');
    }
}
