<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

abstract class BaseParseCommand extends Command
{
    /**
     * The configuration key that holds the source data.
     */
    protected string $configKey;

    /**
     * The model that should receive the parsed payloads.
     */
    protected string $modelClass;

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        /** @var class-string<Model> $model */
        $model = $this->modelClass;

        $items = collect(config($this->configKey, []));

        if ($items->isEmpty()) {
            $this->info('No records were returned by the parser.');

            return self::SUCCESS;
        }

        $created = $items->map(fn (array $payload) => $this->persist($model, $payload))->filter()->count();

        $this->info(sprintf('Stored %d %s.', $created, $this->outputLabel($created)));

        return self::SUCCESS;
    }

    /**
     * Persist the parsed payload.
     */
    protected function persist(string $model, array $payload): ?Model
    {
        /** @var Collection<int, string> $candidates */
        $candidates = collect(['tmdb_id', 'imdb_id', 'omdb_id', 'slug']);

        $identifier = $candidates
            ->first(fn (string $column) => filled(Arr::get($payload, $column)));

        if ($identifier === null) {
            return $model::create($payload);
        }

        return $model::updateOrCreate([
            $identifier => Arr::get($payload, $identifier),
        ], $payload);
    }

    /**
     * Determine the message label for the command output.
     */
    protected function outputLabel(int $count): string
    {
        return $count === 1 ? 'record' : 'records';
    }
}
