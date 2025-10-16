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
        return [
            'workload.required' => __('validation.custom.workload.required'),
            'workload.string' => __('validation.custom.workload.string'),
            'workload.enum' => __('validation.custom.workload.enum'),
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

    public function workload(): ParserWorkload
    {
        $value = $this->input('workload');

        return ParserWorkload::from($value);
    }
}
