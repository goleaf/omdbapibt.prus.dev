<?php

namespace App\Livewire\Forms;

use Livewire\Form;

class ParserEntryDecisionForm extends Form
{
    public string $notes = '';

    /**
     * Validate the form data when rejecting an entry.
     */
    public function ensureValidForRejection(): array
    {
        return $this->validate();
    }

    /**
     * Reset the form state.
     */
    public function clear(): void
    {
        $this->reset('notes');
    }

    public function rules(): array
    {
        return [
            'notes' => ['required', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'notes.required' => trans('parser.moderation.validation.notes_required'),
            'notes.max' => trans('parser.moderation.validation.notes_max'),
        ];
    }

    public function attributes(): array
    {
        return [
            'notes' => trans('parser.moderation.fields.notes'),
        ];
    }
}
