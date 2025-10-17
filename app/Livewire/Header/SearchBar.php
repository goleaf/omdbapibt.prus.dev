<?php

namespace App\Livewire\Header;

use Livewire\Attributes\On;
use Livewire\Component;

class SearchBar extends Component
{
    public string $query = '';

    public array $results = [];

    public bool $showResults = false;

    public bool $isLoading = false;

    public function updatedQuery(): void
    {
        if (strlen($this->query) < 2) {
            $this->reset(['results', 'showResults']);

            return;
        }

        $this->isLoading = true;
        $this->results = $this->search();
        $this->showResults = true;
        $this->isLoading = false;
    }

    protected function search(): array
    {
        // Placeholder search implementation
        // TODO: Implement actual search logic across movies, shows, people
        return [
            'movies' => [],
            'shows' => [],
            'people' => [],
        ];
    }

    public function clear(): void
    {
        $this->reset(['query', 'results', 'showResults']);
    }

    #[On('focusSearch')]
    public function focusSearch(): void
    {
        $this->dispatch('focus-search-input');
    }

    #[On('closeAllDropdowns')]
    public function closeDropdowns(): void
    {
        $this->showResults = false;
    }

    public function render()
    {
        return view('livewire.header.search-bar');
    }
}
