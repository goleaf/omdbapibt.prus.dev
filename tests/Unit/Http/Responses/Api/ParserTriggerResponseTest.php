<?php

namespace Tests\Unit\Http\Responses\Api;

use App\Enums\ParserWorkload;
use App\Http\Responses\Api\ParserTriggerResponse;
use Illuminate\Http\JsonResponse;
use Tests\TestCase;

class ParserTriggerResponseTest extends TestCase
{
    public function test_builds_standardized_accepted_response(): void
    {
        $response = ParserTriggerResponse::from(ParserWorkload::People, 'priority-parsing');

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(JsonResponse::HTTP_ACCEPTED, $response->status());
        $this->assertSame([
            'data' => [
                'status' => 'queued',
                'workload' => ParserWorkload::People->value,
            ],
            'meta' => [
                'queue' => 'priority-parsing',
            ],
        ], $response->getData(true));
    }

    public function test_allows_custom_queue_names(): void
    {
        $response = ParserTriggerResponse::from(ParserWorkload::Movies, 'low-priority');

        $this->assertSame('low-priority', $response->getData(true)['meta']['queue']);
    }
}
