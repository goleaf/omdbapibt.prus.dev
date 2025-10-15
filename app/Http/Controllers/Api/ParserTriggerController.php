<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\Parsing\ExecuteParserPipeline;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ParserTriggerController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'workload' => [
                'required',
                'string',
                Rule::in(config('parser.workloads', ['movies', 'tv', 'people'])),
            ],
        ]);

        $workload = $validated['workload'];

        ExecuteParserPipeline::dispatch($workload);

        return response()->json([
            'status' => 'queued',
            'workload' => $workload,
            'queue' => config('parser.queue'),
        ], 202);
    }
}
