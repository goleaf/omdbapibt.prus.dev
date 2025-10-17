<?php

namespace App\Livewire\Header;

use Livewire\Attributes\On;
use Livewire\Component;

class UserMenu extends Component
{
    public bool $isOpen = false;

    public function toggle(): void
    {
        $this->isOpen = ! $this->isOpen;
    }

    public function close(): void
    {
        $this->isOpen = false;
    }

    #[On('closeAllDropdowns')]
    public function closeDropdowns(): void
    {
        $this->isOpen = false;
    }

    public function render()
    {
        return view('livewire.header.user-menu', [
            'user' => auth()->user(),
        ]);
    }
}
