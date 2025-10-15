<?php

namespace App\Http\Controllers\Api;

use App\Enums\ParserWorkload;
use App\Http\Controllers\Controller;
use App\Http\Responses\Api\ParserTriggerResponse;
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

        $this->authorize('trigger', [ParserEntry::class, $workload]);

        ExecuteParserPipeline::dispatch($workload);

        return ParserTriggerResponse::fromWorkload(
            $workload,
            (string) config('parser.queue', 'parsing'),
        );
    }
}
