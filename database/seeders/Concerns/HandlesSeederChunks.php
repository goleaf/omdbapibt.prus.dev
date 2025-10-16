<?php

namespace Database\Seeders\Concerns;

use Closure;

trait HandlesSeederChunks
{
    /**
     * Execute the given callback for the provided total count using chunk sizes.
     */
    protected function forChunkedCount(int $total, int $chunkSize, Closure $callback): void
    {
        $remaining = $total;

        while ($remaining > 0) {
            $count = min($chunkSize, $remaining);

            $callback($count);

            $remaining -= $count;
        }
    }
}
