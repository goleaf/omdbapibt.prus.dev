<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ParserTriggerRequest;
use App\Http\Responses\Api\ParserTriggerResponse;
use App\Jobs\Parsing\ExecuteParserPipeline;
use App\Models\ParserEntry;

class ParserTriggerController extends Controller
{
    public function __invoke(ParserTriggerRequest $request): ParserTriggerResponse
    {
        $workload = $request->workload();

        $this->authorize('trigger', [ParserEntry::class, $workload]);

        ExecuteParserPipeline::dispatch($workload);

        return ParserTriggerResponse::fromWorkload($workload);
    }
}
