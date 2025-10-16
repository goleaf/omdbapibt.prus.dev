<?php

namespace App\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Livewire\Component;

class HomePage extends Component
{
    /**
     * Hero metric cards displayed in the highlights section.
     *
     * @var array<int, array<string, string>>
     */
    public array $heroStats = [];

    /**
     * Primary call-to-action button metadata.
     *
     * @var array<string, string>
     */
    public array $primaryCta = [];

    /**
     * Secondary call-to-action button metadata.
     *
     * @var array<string, string>
     */
    public array $secondaryCta = [];

    /**
     * Label describing when the hero metrics were last refreshed.
     */
    public string $lastUpdatedLabel = '';

    public function mount(): void
    {
        $locale = app()->getLocale();

        $this->heroStats = [
            [
                'label' => __('Streaming regions'),
                'value' => '87',
            ],
            [
                'label' => __('Flux-enabled components'),
                'value' => '42',
            ],
            [
                'label' => __('Library uptime'),
                'value' => '99.9%',
            ],
        ];

        $this->primaryCta = [
            'label' => __('Start browsing'),
            'href' => route('browse', ['locale' => $locale]),
            'icon' => 'play',
        ];

        $this->secondaryCta = [
            'label' => __('View membership tiers'),
            'href' => route('pricing', ['locale' => $locale]),
            'icon' => 'sparkles',
        ];

        $this->lastUpdatedLabel = __('Updated :date across all catalog sources.', [
            'date' => Carbon::now()->format('M j, Y'),
        ]);
    }

    public function render(): View
    {
        return view('pages.home')
            ->layout('layouts.app', [
                'title' => __('Flux-powered cinematic discovery'),
            ]);
    }
}
