<?php

namespace App\Http\Requests\Api;

use App\Enums\ParserWorkload;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ParserTriggerRequest extends FormRequest
{
    /**
     * @return array<string, array<int, string|ValidationRule>>
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

    public function authorize(): bool
    {
        return true;
    }

    public function messages(): array
    {
        $enumMessage = __('validation.custom.workload.enum');

        return [
            'workload.required' => __('validation.custom.workload.required'),
            'workload.string' => __('validation.custom.workload.string'),
            'workload.enum' => $enumMessage,
            'workload.Enum' => $enumMessage,
            'workload.Illuminate\Validation\Rules\Enum' => $enumMessage,
        ];
    }

    public function workload(): ParserWorkload
    {
        /** @var string $value */
        $value = $this->validated('workload');

        return ParserWorkload::from($value);
    }
}
