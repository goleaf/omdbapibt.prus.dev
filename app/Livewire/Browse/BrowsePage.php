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
                    ? __('Member area')
                    : __('Browse movies and shows'),
                'subheader' => $this->locked
                    ? __('Sign in with an active subscription to open the full library and all of the sorting tools.')
                    : __('Use the filters on the left to narrow things down or scroll through the lists for quick ideas.'),
            ]);
    }
}
