<?php

namespace Tests\Unit\Http\Requests\Api;

use App\Enums\ParserWorkload;
use App\Http\Requests\Api\ParserTriggerRequest;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class ParserTriggerRequestTest extends TestCase
{
    public function test_it_accepts_supported_workloads(): void
    {
        $request = new ParserTriggerRequest;

        foreach (ParserWorkload::cases() as $workload) {
            $validator = Validator::make(
                ['workload' => $workload->value],
                $request->rules(),
                $request->messages()
            );

            $this->assertFalse(
                $validator->fails(),
                sprintf('Expected "%s" to be accepted as a valid workload.', $workload->value)
            );
        }
    }

    public function test_it_rejects_invalid_workload_with_localized_message(): void
    {
        app()->setLocale('es');

        $request = new ParserTriggerRequest;

        $validator = Validator::make(
            ['workload' => 'invalid'],
            $request->rules(),
            $request->messages()
        );

        $this->assertTrue($validator->fails());
        $this->assertSame(
            [__('validation.enum', ['attribute' => __('validation.attributes.workload')])],
            $validator->errors()->get('workload')
        );
    }
}
