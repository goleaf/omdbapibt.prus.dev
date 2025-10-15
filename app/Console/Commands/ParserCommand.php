<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

abstract class ParserCommand extends Command
{
    protected string $configKey = '';

    protected string $modelLabel = 'record';

    public function handle(): int
    {
        $records = collect($this->resolveConfiguredRecords());

        if ($records->isEmpty()) {
            $this->components->warn(sprintf(
                'No %s were defined in the parser configuration.',
                Str::plural($this->modelLabel)
            ));

            return self::SUCCESS;
        }

        $processed = 0;

        $records->each(function (array $payload) use (&$processed): void {
            $model = $this->hydrateRecord($payload);

            $processed++;

            $this->components->info(sprintf(
                'Upserted %s #%s (%s).',
                $this->modelLabel,
                $model->getKey(),
                $this->resolveDisplayName($model, $payload)
            ));
        });

        $this->components->info(sprintf(
            'Processed %d %s from parser configuration.',
            $processed,
            Str::plural($this->modelLabel, $processed)
        ));

        return self::SUCCESS;
    }

    abstract protected function hydrateRecord(array $payload): Model;

    protected function resolveConfiguredRecords(): array
    {
        return Arr::get(config('parser', []), $this->configKey, []);
    }

    protected function resolveDisplayName(Model $model, array $payload): string
    {
        foreach (['title', 'name', 'slug'] as $key) {
            if (! empty($payload[$key])) {
                return (string) $payload[$key];
            }

            if ($model->getAttribute($key)) {
                return (string) $model->getAttribute($key);
            }
        }

        return (string) $model->getKey();
    }
}
