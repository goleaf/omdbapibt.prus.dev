<?php

namespace App\Http\Requests\Api;

use App\Enums\ParserWorkload;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ParserTriggerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string|\Illuminate\Contracts\Validation\ValidationRule|\Closure>>
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

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'workload.required' => __('validation.parser_trigger.workload.required'),
            'workload.string' => __('validation.parser_trigger.workload.string'),
            'workload.enum' => __('validation.parser_trigger.workload.enum'),
            'workload.Enum' => __('validation.parser_trigger.workload.enum'),
        ];
    }

    public function workload(): ParserWorkload
    {
        $workload = $this->validated('workload');

        return ParserWorkload::from(is_string($workload) ? $workload : (string) $this->input('workload'));
    }
}
