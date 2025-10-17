<?php

namespace App\Livewire\Header;

use Livewire\Attributes\On;
use Livewire\Component;

class MobilePanel extends Component
{
    public bool $isOpen = false;

    #[On('toggleMobileMenu')]
    public function toggleMobileMenu(): void
    {
        $this->isOpen = ! $this->isOpen;
    }

    public function close(): void
    {
        $this->isOpen = false;
    }

    public function render()
    {
        return view('livewire.header.mobile-panel', [
            'user' => auth()->user(),
            'hasLogin' => \Route::has('login'),
            'hasRegister' => \Route::has('register'),
        ]);
    }
}
