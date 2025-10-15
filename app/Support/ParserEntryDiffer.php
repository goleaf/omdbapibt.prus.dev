<?php

namespace App\Support;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class ParserEntryDiffer
{
    /**
     * Build a flattened diff showing how incoming payload differs from the baseline snapshot.
     *
     * @param  array<array-key, mixed>  $baseline
     * @param  array<array-key, mixed>  $incoming
     * @return array<int, array{key: string, before: mixed, after: mixed}>
     */
    public function diff(array $baseline, array $incoming): array
    {
        $baselineDot = Arr::dot($baseline);
        $incomingDot = Arr::dot($incoming);

        $keys = Collection::make(array_keys($baselineDot))
            ->merge(array_keys($incomingDot))
            ->unique()
            ->sort()
            ->values();

        return $keys->map(function (string $key) use ($baselineDot, $incomingDot): ?array {
            $before = $baselineDot[$key] ?? null;
            $after = $incomingDot[$key] ?? null;

            if ($before === $after) {
                return null;
            }

            return [
                'key' => $key,
                'before' => $before,
                'after' => $after,
            ];
        })->filter()->values()->all();
    }
}
