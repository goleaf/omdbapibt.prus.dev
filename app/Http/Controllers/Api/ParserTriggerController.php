<?php

namespace App\Http\Controllers\Api;

use App\Enums\ParserWorkload;
use App\Http\Controllers\Controller;
use App\Jobs\Parsing\ExecuteParserPipeline;
use App\Models\ParserEntry;
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
                Rule::enum(ParserWorkload::class),
            ],
        ]);

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
