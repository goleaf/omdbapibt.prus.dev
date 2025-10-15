<?php

namespace App\Livewire\Forms;

use Livewire\Form;

class ParserEntryDecisionForm extends Form
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
            'notes.required' => __('parser.moderation.notes_required'),
            'notes.string' => __('parser.moderation.notes_string'),
            'notes.max' => __('parser.moderation.notes_max'),
        ];
    }
}
