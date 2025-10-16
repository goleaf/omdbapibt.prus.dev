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

    public static function from(ParserWorkload $workload, string $queue): JsonResponse
    {
        return (new self($workload, $queue))->toResponse(request());
    }

    public function toResponse($request): JsonResponse
    {
        return response()->json([
            'data' => [
                'status' => 'queued',
                'workload' => $this->workload->value,
            ],
            'meta' => [
                'queue' => $this->queue,
            ],
        ], 202);
    }
}
