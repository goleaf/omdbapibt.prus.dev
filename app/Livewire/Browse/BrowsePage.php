<?php

namespace App\Livewire\Browse;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class BrowsePage extends Component
{
    public bool $locked = true;

    public function mount(): void
    {
        $user = Auth::user();

        if ($user && $user->hasPremiumAccess()) {
            $this->locked = false;
        }
    }

    public function render(): View
    {
        return view('livewire.browse.browse-page')
            ->layout('layouts.app', [
                'title' => __('Browse the catalog'),
                'header' => $this->locked
                    ? __('Unlock the full OMDb experience')
                    : __('Browse trending movies and shows'),
                'subheader' => $this->locked
                    ? __('Sign in and upgrade to explore the complete catalog, streaming options, and personalized filters.')
                    : __('Use filters, curated categories, and real-time streaming availability to zero in on what to watch next.'),
            ]);
    }
}
