<?php

namespace App\Livewire\Reviews;

use App\Livewire\Forms\ReviewSubmissionForm;
use App\Models\Review;
use App\Support\HtmlSanitizer;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ReviewForm extends Component
{
    public ReviewSubmissionForm $form;

    public string $statusMessage = '';

    public function submit(): void
    {
        $this->form->validate();

        $user = Auth::user();

        if (! $user) {
            abort(403, 'Authentication required.');
        }

        $sanitized = HtmlSanitizer::clean($this->form->body);

        Review::create([
            'user_id' => $user->id,
            'movie_title' => $this->form->movieTitle,
            'rating' => $this->form->rating,
            'body' => $sanitized,
        ]);

        $this->form->reset();
        $this->form->rating = 5;
        $this->statusMessage = __('reviews.messages.submitted');

        $this->dispatch('review-submitted');
    }

    public function render()
    {
        return view('livewire.reviews.review-form');
    }
}
