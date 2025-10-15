<?php

namespace App\Services\Parser\Hydrators;

use App\Enums\ParserEntryStatus;
use App\Models\ParserEntry;
use App\Services\Parser\Contracts\Hydrator;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

abstract class ModelHydrator implements Hydrator
{
    public function __construct(protected string $parserKey) {}

    public function hydrate(int $chunkSize, array $options = []): int
    {
        $query = $this->newQuery();
        $model = $query->getModel();

        $query->orderByDesc('updated_at')
            ->orderByDesc($model->getKeyName())
            ->limit($chunkSize);

        if (! empty($options['since'])) {
            $timestamp = $this->parseSinceOption($options['since']);

            if ($timestamp) {
                $query->where('updated_at', '>=', $timestamp);
            }
        }

        $models = $query->get();

        $created = 0;

        foreach ($models as $model) {
            $entry = ParserEntry::updateOrCreate(
                [
                    'subject_type' => $model->getMorphClass(),
                    'subject_id' => $model->getKey(),
                    'parser' => $this->parserKey,
                ],
                [
                    'payload' => $this->buildPayload($model),
                    'baseline_snapshot' => $this->baseline($model),
                    'status' => ParserEntryStatus::Pending,
                ],
            );

            if ($entry->wasRecentlyCreated) {
                $created++;
            }
        }

        return $created;
    }

    abstract protected function newQuery(): Builder;

    protected function buildPayload(Model $model): array
    {
        return array_filter([
            'tmdb_id' => $model->tmdb_id ?? null,
            'imdb_id' => $model->imdb_id ?? null,
            'slug' => $model->slug ?? null,
        ], fn ($value) => $value !== null);
    }

    protected function baseline(Model $model): array
    {
        return $model->toArray();
    }

    protected function parseSinceOption(string $value): ?CarbonImmutable
    {
        try {
            return CarbonImmutable::parse($value);
        } catch (\Throwable) {
            return null;
        }
    }
}
