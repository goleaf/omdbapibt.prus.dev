<?php

namespace App\Http\Controllers\Api;

use App\Enums\ParserWorkload;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ParserTriggerRequest;
use App\Jobs\Parsing\ExecuteParserPipeline;
use App\Models\ParserEntry;
use Illuminate\Http\JsonResponse;

class ParserTriggerController extends Controller
{
    public function __invoke(ParserTriggerRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $workload = ParserWorkload::from($validated['workload']);

        $this->authorize('trigger', ParserEntry::class);

        ExecuteParserPipeline::dispatch($workload);

        return response()->json([
            'status' => 'queued',
            'workload' => $workload->value,
            'queue' => config('parser.queue'),
        ], 202);
    }
}
