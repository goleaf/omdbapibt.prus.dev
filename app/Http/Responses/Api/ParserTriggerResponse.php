<?php

namespace App\Http\Responses\Api;

use App\Enums\ParserWorkload;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;

class ParserTriggerResponse implements Responsable
{
    public function __construct(
        private readonly ParserWorkload $workload,
        private readonly string $queue,
    ) {}

    public function toResponse($request): JsonResponse
    {
        return response()->json([
            'status' => 'queued',
            'workload' => $this->workload->value,
            'queue' => $this->queue,
        ], 202);
    }
}
