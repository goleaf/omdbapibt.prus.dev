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
                'title' => __('browse.meta.title'),
                'header' => $this->locked
                    ? __('browse.meta.locked_header')
                    : __('browse.meta.unlocked_header'),
                'subheader' => $this->locked
                    ? __('browse.meta.locked_subheader')
                    : __('browse.meta.unlocked_subheader'),
            ]);
    }
}
