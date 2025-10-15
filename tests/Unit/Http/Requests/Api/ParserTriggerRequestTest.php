<?php

namespace Tests\Unit\Http\Requests\Api;

use App\Enums\ParserWorkload;
use App\Http\Requests\Api\ParserTriggerRequest;
use Illuminate\Validation\Rules\Enum as EnumRule;
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

        $rules = $request->rules();

        $this->assertArrayHasKey('workload', $rules);
        $this->assertCount(3, $rules['workload']);
        $this->assertSame('required', $rules['workload'][0]);
        $this->assertSame('string', $rules['workload'][1]);
        $this->assertInstanceOf(EnumRule::class, $rules['workload'][2]);
        $this->assertEquals(new EnumRule(ParserWorkload::class), $rules['workload'][2]);
    }

    /**
     * @dataProvider localeExpectationProvider
     */
    public function test_it_returns_localized_messages_for_invalid_workloads(string $locale, array $expected): void
    {
        $this->app->setLocale($locale);

        $request = new ParserTriggerRequest;
        $messages = $request->messages();

        $this->assertSame($expected['enum'], $messages['workload.enum']);
        $this->assertSame($expected['required'], $messages['workload.required']);
        $this->assertSame($expected['string'], $messages['workload.string']);

        $this->app->setLocale('en');
    }

    /**
     * @return array<string, array{string, array{enum: string, required: string, string: string}}>
     */
    public static function localeExpectationProvider(): array
    {
        return [
            'english' => [
                'en',
                [
                    'enum' => 'The selected workload is invalid.',
                    'required' => 'Please select a workload to trigger.',
                    'string' => 'The workload value must be a string.',
                ],
            ],
            'spanish' => [
                'es',
                [
                    'enum' => 'La carga de trabajo seleccionada no es válida.',
                    'required' => 'Selecciona una carga de trabajo para ejecutar.',
                    'string' => 'El valor de la carga de trabajo debe ser una cadena.',
                ],
            ],
            'french' => [
                'fr',
                [
                    'enum' => "La charge de travail sélectionnée n'est pas valide.",
                    'required' => 'Veuillez sélectionner une charge de travail à lancer.',
                    'string' => 'La valeur de la charge de travail doit être une chaîne de caractères.',
                ],
            ],
        ];
    }
}
