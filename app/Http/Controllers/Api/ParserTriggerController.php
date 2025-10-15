<?php

namespace App\Http\Controllers\Api;

use App\Enums\ParserWorkload;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ParserTriggerRequest;
use App\Http\Responses\Api\ParserTriggerResponse;
use App\Jobs\Parsing\ExecuteParserPipeline;
use App\Models\ParserEntry;
use Illuminate\Http\JsonResponse;

class ParserTriggerController extends Controller
{
    public function __invoke(ParserTriggerRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $workload = ParserWorkload::from($validated['workload']);

        $this->authorize('trigger', [ParserEntry::class, $workload]);

        ExecuteParserPipeline::dispatch($workload);

        return ParserTriggerResponse::fromWorkload(
            $workload,
            (string) config('parser.queue', 'parsing'),
        );
    }
}
