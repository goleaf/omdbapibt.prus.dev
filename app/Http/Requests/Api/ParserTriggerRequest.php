<?php

namespace App\Http\Requests\Api;

use App\Enums\ParserWorkload;
use App\Models\ParserEntry;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ParserTriggerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('trigger', ParserEntry::class) ?? false;
    }

    /**
     * @return array<string, array<int, string|Rule>>
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
            'workload.required' => trans('parser.trigger.workload_required'),
            'workload.string' => trans('parser.trigger.workload_string'),
            'workload.enum' => trans('parser.trigger.workload_enum'),
        ];
    }

    public function workload(): ParserWorkload
    {
        /** @var string $workload */
        $workload = $this->validated('workload');

        return ParserWorkload::from($workload);
    }
}
