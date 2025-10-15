<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\Parsing\ExecuteParserPipeline;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ParserTriggerController extends Controller
{
    /**
     * @var list<string>
     */
    private const ALLOWED_WORKLOADS = ['movies', 'tv', 'people'];

    public function __invoke(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'workload' => ['required', 'string', Rule::in(self::ALLOWED_WORKLOADS)],
        ]);

        ExecuteParserPipeline::dispatch($validated['workload']);

        return response()->json([
            'status' => 'queued',
            'workload' => $validated['workload'],
        ], 202);
    }
}
