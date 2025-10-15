<?php

namespace App\Http\Requests\Api;

use App\Enums\ParserWorkload;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class ParserTriggerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'workload' => [
                'required',
                'string',
                Rule::enum(ParserWorkload::class),
            ],
        ];
    }

    public function validatedWorkload(): ParserWorkload
    {
        /** @var string $workload */
        $workload = $this->validated('workload');

        return ParserWorkload::from($workload);
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'workload.required' => __('parser.trigger.workload_required'),
            'workload.string' => __('parser.trigger.workload_string'),
            'workload.enum' => __('parser.trigger.workload_enum'),
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'workload' => __('parser.trigger.workload_attribute'),
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        $errors = $validator->errors();

        throw new HttpResponseException(response()->json([
            'message' => $errors->first(),
            'errors' => $errors->toArray(),
        ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY));
    }

    public function validatedWorkload(): ParserWorkload
    {
        $validated = $this->validated();

        return ParserWorkload::from($validated['workload']);
    }
}
