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
     * @return array<string, array<int, string|Rule>>
     */
    public function rules(): array
    {
        return [
            'workload' => ['required', 'string', Rule::enum(ParserWorkload::class)],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'workload.required' => __('validation.custom.workload.required'),
            'workload.string' => __('validation.custom.workload.string'),
            'workload.enum' => __('validation.custom.workload.enum'),
        ];
    }
}
