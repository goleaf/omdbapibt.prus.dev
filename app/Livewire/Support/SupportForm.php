<?php

namespace App\Livewire\Support;

use App\Models\SupportRequest;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class SupportForm extends Component
{
    public string $name = '';

    public string $email = '';

    public string $subject = '';

    public string $message = '';

    public string $statusMessage = '';

    /**
     * @return array<string, array<int, string>>
     */
    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:2000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function messages(): array
    {
        return [
            'name.required' => __('support.validation.name.required'),
            'name.string' => __('support.validation.name.string'),
            'name.max' => __('support.validation.name.max'),
            'email.required' => __('support.validation.email.required'),
            'email.string' => __('support.validation.email.string'),
            'email.email' => __('support.validation.email.email'),
            'email.max' => __('support.validation.email.max'),
            'subject.required' => __('support.validation.subject.required'),
            'subject.string' => __('support.validation.subject.string'),
            'subject.max' => __('support.validation.subject.max'),
            'message.required' => __('support.validation.message.required'),
            'message.string' => __('support.validation.message.string'),
            'message.max' => __('support.validation.message.max'),
        ];
    }

    public function submit(): void
    {
        $validated = $this->validate(
            rules: $this->rules(),
            messages: $this->messages(),
        );

        $supportRequest = SupportRequest::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'subject' => $validated['subject'],
            'message' => $validated['message'],
            'status' => SupportRequest::STATUS_PENDING,
        ]);

        $this->reset(['name', 'email', 'subject', 'message']);
        $this->resetValidation();
        $this->statusMessage = __('support.status.submitted');

        $this->dispatch('support-request-submitted', id: $supportRequest->id);
    }

    public function render(): View
    {
        return view('livewire.support.support-form');
    }
}
