<?php

namespace App\Livewire\Reviews;

use App\Livewire\Forms\ReviewSubmissionForm;
use App\Models\Movie;
use App\Models\Review;
use App\Support\HtmlSanitizer;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Component;

class ReviewForm extends Component
{
    public ReviewSubmissionForm $form;

    public string $statusMessage = '';

    public function submit(): void
    {
        $validated = $this->form->validate();

        $user = Auth::user();

        if (! $user) {
            abort(403, 'Authentication required.');
        }

        $sanitized = HtmlSanitizer::clean($validated['body']);

        Review::create([
            'user_id' => $user->id,
            'movie_id' => $validated['movieId'],
            'rating' => $validated['rating'],
            'body' => $sanitized,
        ]);

        $this->form->reset();
        $this->form->rating = 5;
        $this->form->movieId = null;
        $this->statusMessage = __('reviews.status.submitted');

        $this->dispatch('review-submitted');
    }

    public function render(): View
    {
        $locale = app()->getLocale();

        $movies = Movie::query()
            ->select(['id', 'title'])
            ->orderBy('id')
            ->get()
            ->mapWithKeys(fn (Movie $movie) => [
                $movie->id => $movie->localizedTitle($locale),
            ]);

        return view('livewire.reviews.review-form', [
            'movies' => $movies,
        ]);
    }
}
