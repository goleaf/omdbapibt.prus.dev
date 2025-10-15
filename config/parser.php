<?php

return [
    'queue' => env('PARSER_QUEUE', 'parsing'),

    'workloads' => array_values(array_filter(array_map(
        static fn (string $workload): string => trim($workload),
        explode(',', (string) env('PARSER_ALLOWED_WORKLOADS', 'movies,tv,people'))
    ))),
];
