<?php

namespace App\Http\Requests\Api;

use App\Enums\ParserWorkload;
use App\Models\ParserEntry;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class ParserTriggerRequest extends FormRequest
{
    public function authorize(): bool
    {
        $candidate = ParserWorkload::tryFrom((string) $this->input('workload'))
            ?? ParserWorkload::Movies;

        return Gate::allows('trigger', [ParserEntry::class, $candidate]);
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

    public function workload(): ParserWorkload
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
            'workload.required' => __('parser.trigger.validation.workload.required'),
            'workload.string' => __('parser.trigger.validation.workload.string'),
            'workload.enum' => __('parser.trigger.validation.workload.enum'),
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'workload' => __('parser.trigger.attributes.workload'),
        ];
    }
}
