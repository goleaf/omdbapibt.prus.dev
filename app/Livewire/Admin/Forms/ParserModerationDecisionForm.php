<?php

namespace App\Livewire\Admin\Forms;

use Livewire\Form;

class ParserModerationDecisionForm extends Form
{
    public string $notes = '';

    public function rules(): array
    {
        return [
            'notes' => ['required', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'notes.required' => trans('parser.moderation.validation.notes.required'),
            'notes.string' => trans('parser.moderation.validation.notes.string'),
            'notes.max' => trans('parser.moderation.validation.notes.max'),
        ];
    }

    public function attributes(): array
    {
        return [
            'notes' => trans('parser.moderation.decision.fields.notes'),
        ];
    }
}
