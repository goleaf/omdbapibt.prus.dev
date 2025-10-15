<?php

namespace App\Livewire\Reviews;

use App\Livewire\Forms\ReviewSubmissionForm;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Component;

class ReviewForm extends Component
{
    public ReviewSubmissionForm $form;

    public string $statusMessage = '';

    public function submit(): void
    {
        $user = Auth::user();

        if (! $user) {
            abort(403, 'Authentication required.');
        }

        $this->form->submitFor($user);
        $this->statusMessage = __('reviews.messages.submitted');

        $this->dispatch('review-submitted');
    }

    public function render(): View
    {
        return view('livewire.reviews.review-form');
    }
}
