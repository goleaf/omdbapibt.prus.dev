<?php

namespace Tests\Unit\Http\Requests\Api;

use App\Enums\ParserWorkload;
use App\Http\Requests\Api\ParserTriggerRequest;
use Illuminate\Validation\Rule;
use Tests\TestCase;

class ParserTriggerRequestTest extends TestCase
{
    public function test_it_allows_any_user(): void
    {
        $request = new ParserTriggerRequest;

        $this->assertTrue($request->authorize());
    }

    public function test_it_defines_expected_rules(): void
    {
        $request = new ParserTriggerRequest;

        $this->assertEquals([
            'workload' => [
                'required',
                'string',
                Rule::enum(ParserWorkload::class),
            ],
        ], $request->rules());
    }

    /**
     * @dataProvider localizedMessagesProvider
     *
     * @param  array<string, string>  $expected
     */
    public function test_it_returns_localized_messages(string $locale, array $expected): void
    {
        app()->setLocale($locale);

        $request = new ParserTriggerRequest;

        $this->assertSame($expected, $request->messages());
    }

    /**
     * @return array<string, array{0: string, 1: array<string, string>}>
     */
    public static function localizedMessagesProvider(): array
    {
        return [
            'english' => ['en', [
                'workload.required' => 'Please select a parser workload to trigger.',
                'workload.string' => 'The parser workload must be provided as text.',
                'workload.enum' => 'The selected parser workload is invalid.',
            ]],
            'spanish' => ['es', [
                'workload.required' => 'Selecciona una carga de procesamiento para iniciar.',
                'workload.string' => 'La carga de procesamiento debe enviarse como texto.',
                'workload.enum' => 'La carga de procesamiento seleccionada no es válida.',
            ]],
            'french' => ['fr', [
                'workload.required' => 'Veuillez sélectionner une charge de traitement à lancer.',
                'workload.string' => 'La charge de traitement doit être fournie sous forme de texte.',
                'workload.enum' => 'La charge de traitement sélectionnée n\'est pas valide.',
            ]],
        ];
    }

    /**
     * @dataProvider localizedAttributesProvider
     *
     * @param  array<string, string>  $expected
     */
    public function test_it_exposes_localized_attribute_names(string $locale, array $expected): void
    {
        app()->setLocale($locale);

        $request = new ParserTriggerRequest;

        $this->assertSame($expected, $request->attributes());
    }

    /**
     * @return array<string, array{0: string, 1: array<string, string>}>
     */
    public static function localizedAttributesProvider(): array
    {
        return [
            'english' => ['en', ['workload' => 'parser workload']],
            'spanish' => ['es', ['workload' => 'carga de procesamiento']],
            'french' => ['fr', ['workload' => 'charge de traitement']],
        ];
    }
}
