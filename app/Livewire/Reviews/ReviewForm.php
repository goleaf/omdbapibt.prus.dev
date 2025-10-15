<?php

namespace App\Livewire\Reviews;

use App\Models\Review;
use App\Support\HtmlSanitizer;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ReviewForm extends Component
{
    public string $movieTitle = '';

    public int $rating = 5;

    public string $body = '';

    public string $statusMessage = '';

    protected function rules(): array
    {
        return [
            'movieTitle' => ['required', 'string', 'max:255'],
            'rating' => ['required', 'integer', 'between:1,5'],
            'body' => ['required', 'string', 'max:2000'],
        ];
    }

    public function submit(): void
    {
        $this->validate();

        $user = Auth::user();

        if (! $user) {
            abort(403, 'Authentication required.');
        }

        $sanitized = HtmlSanitizer::clean($this->body);

        Review::create([
            'user_id' => $user->id,
            'movie_title' => $this->movieTitle,
            'rating' => $this->rating,
            'body' => $sanitized,
        ]);

        $this->reset(['movieTitle', 'rating', 'body']);
        $this->rating = 5;
        $this->statusMessage = 'Review submitted successfully.';

        $this->dispatch('review-submitted');
    }

    public function render()
    {
        return view('livewire.reviews.review-form');
    }
}
