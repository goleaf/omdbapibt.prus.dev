<?php

namespace App\Services\Parser;

use App\Services\Parser\Contracts\Hydrator;
use Illuminate\Contracts\Container\Container;
use InvalidArgumentException;

class HydratorManager
{
    public function __construct(protected Container $container) {}

    public function hydrate(string $target, ?int $chunkSize = null, array $options = []): int
    {
        $configuration = $this->configurationFor($target);

        $hydrator = $this->resolveHydrator($configuration['hydrator']);
        $chunk = $this->resolveChunkSize($configuration, $chunkSize);

        return $hydrator->hydrate($chunk, $options);
    }

    public function configurationFor(string $target): array
    {
        $configuration = config("parser.targets.{$target}");

        if (! is_array($configuration) || empty($configuration['hydrator'])) {
            throw new InvalidArgumentException("Parser target [{$target}] is not configured.");
        }

        return $configuration;
    }

    protected function resolveHydrator(string $hydrator): Hydrator
    {
        $resolved = $this->container->make($hydrator);

        if (! $resolved instanceof Hydrator) {
            throw new InvalidArgumentException(sprintf('Parser hydrator [%s] must implement %s.', $hydrator, Hydrator::class));
        }

        return $resolved;
    }

    protected function resolveChunkSize(array $configuration, ?int $chunkSize): int
    {
        if (is_int($chunkSize) && $chunkSize > 0) {
            return $chunkSize;
        }

        $configured = (int) ($configuration['chunk_size'] ?? 0);

        if ($configured > 0) {
            return $configured;
        }

        return max(1, (int) config('parser.default_chunk', 25));
    }
}
