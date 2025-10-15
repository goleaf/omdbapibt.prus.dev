<?php

namespace App\Services\Parser\Contracts;

interface Hydrator
{
    /**
     * Hydrate parser entries for the configured target.
     */
    public function hydrate(int $chunkSize, array $options = []): int;
}
