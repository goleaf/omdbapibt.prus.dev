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
            'workload.required' => __('parser.trigger.workload_required'),
            'workload.string' => __('parser.trigger.workload_string'),
            'workload.enum' => __('parser.trigger.workload_enum'),
        ];
    }

    public function workload(): ParserWorkload
    {
        return [
            'workload' => __('parser.trigger.workload_attribute'),
        ];
    }

        return ParserWorkload::from($value);
    }
}
