<?php

namespace App\Http\Responses\Api;

use App\Enums\ParserWorkload;
use Illuminate\Http\JsonResponse;

class ParserTriggerResponse
{
    public static function fromWorkload(ParserWorkload $workload, string $queue): JsonResponse
    {
        return response()->json([
            'status' => 'queued',
            'workload' => $workload->value,
            'queue' => $queue,
        ], JsonResponse::HTTP_ACCEPTED);
    }
}
