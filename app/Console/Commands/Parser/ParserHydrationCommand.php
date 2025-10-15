<?php

namespace App\Console\Commands\Parser;

use App\Services\Parser\HydratorManager;
use Illuminate\Console\Command;

abstract class ParserHydrationCommand extends Command
{
    public const OPTIONS = '{--chunk= : Override the configured chunk size.}{--since= : Hydrate items updated on or after the given ISO8601 timestamp.}';

    public function __construct(protected HydratorManager $manager)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $chunk = $this->option('chunk');
        $since = $this->option('since');

        $processed = $this->manager->hydrate(
            $this->targetKey(),
            $chunk ? (int) $chunk : null,
            array_filter([
                'since' => $since,
            ])
        );

        $this->components->info(sprintf(
            'Hydration dispatched for %s (%d records queued).',
            $this->targetLabel(),
            $processed
        ));

        return self::SUCCESS;
    }

    abstract protected function targetKey(): string;

    protected function targetLabel(): string
    {
        $configuration = $this->manager->configurationFor($this->targetKey());

        return $configuration['label'] ?? $this->targetKey();
    }
}
