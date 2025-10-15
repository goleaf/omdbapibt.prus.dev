<?php

namespace App\Http\Responses\Api;

use App\Enums\ParserWorkload;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ParserTriggerResponse implements Responsable
{
    public function __construct(
        private readonly ParserWorkload $workload,
        private readonly string $queue,
        private readonly int $status = Response::HTTP_ACCEPTED,
    ) {}

    public static function fromWorkload(ParserWorkload $workload): self
    {
        return new self($workload, (string) config('parser.queue', 'parsing'));
    }

    public function toResponse($request): JsonResponse
    {
        return response()->json([
            'status' => 'queued',
            'workload' => $this->workload->value,
            'queue' => $this->queue,
        ], $this->status);
    }
}
