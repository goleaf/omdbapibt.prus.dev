<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ParserTriggerRequest;
use App\Jobs\Parsing\ExecuteParserPipeline;
use Illuminate\Http\JsonResponse;

class ParserTriggerController extends Controller
{
    public function __invoke(ParserTriggerRequest $request): JsonResponse
    {
        $request->validated();

        $workload = $request->workload();

        ExecuteParserPipeline::dispatch($workload);

        return response()->json([
            'status' => 'queued',
            'workload' => $workload->value,
            'queue' => config('parser.queue'),
        ], 202);
    }
}
