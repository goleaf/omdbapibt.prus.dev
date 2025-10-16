<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class ControlPanel extends Component
{
    use AuthorizesRequests;

    public function mount(): void
    {
        $this->authorize('viewAny', User::class);
    }

    public function render(): View
    {
        return view('livewire.admin.control-panel');
    }
}
