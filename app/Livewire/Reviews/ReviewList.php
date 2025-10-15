<?php

namespace App\Livewire\Reviews;

use App\Models\Review;
use Livewire\Component;
use Livewire\WithPagination;

class ReviewList extends Component
{
    use WithPagination;

    protected $listeners = ['review-submitted' => '$refresh'];

    public function render()
    {
        $reviews = Review::query()
            ->latest()
            ->paginate(5);

        return view('livewire.reviews.review-list', [
            'reviews' => $reviews,
        ]);
    }
}
