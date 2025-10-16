<?php

namespace Tests\Unit\Http\Requests\Api;

use App\Enums\ParserWorkload;
use App\Http\Requests\Api\ParserTriggerRequest;
use Illuminate\Support\Facades\Validator;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ParserTriggerRequestTest extends TestCase
{
    #[DataProvider('validWorkloadProvider')]
    public function test_accepts_supported_workloads(string $workload): void
    {
        $request = new ParserTriggerRequest;

        $validator = Validator::make([
            'workload' => $workload,
        ], $request->rules(), $request->messages(), $request->attributes());

        $this->assertTrue($validator->passes());
    }

    /**
     * @return array<int, array{0: string}>
     */
    public static function validWorkloadProvider(): array
    {
        return array_map(
            static fn (ParserWorkload $workload): array => [$workload->value],
            ParserWorkload::cases(),
        );
    }

    public function test_rejects_invalid_workload(): void
    {
        $request = new ParserTriggerRequest;

        $validator = Validator::make([
            'workload' => 'invalid',
        ], $request->rules(), $request->messages(), $request->attributes());

        $this->assertFalse($validator->passes());
        $this->assertSame(
            trans('parser.trigger.workload_enum'),
            $validator->errors()->first('workload'),
        );
    }

    public function test_workload_accessor_returns_enum_instance(): void
    {
        $request = new ParserTriggerRequest;
        $expected = ParserWorkload::Movies;

        $request->merge([
            'workload' => $expected->value,
        ]);

        $this->assertSame($expected, $request->workload());
    }
}
