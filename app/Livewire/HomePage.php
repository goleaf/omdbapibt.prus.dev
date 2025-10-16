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

    /**
     * Primary marketing statement displayed beneath the hero heading.
     */
    public string $heroTagline = '';

    public function mount(): void
    {
        $locale = app()->getLocale();

        $this->heroTagline = trans('ui.home.tagline');

        $this->heroStats = [
            [
                'label' => trans('ui.home.stats.streaming_regions'),
                'value' => '87',
            ],
            [
                'label' => trans('ui.home.stats.flux_components'),
                'value' => '42',
            ],
            [
                'label' => trans('ui.home.stats.library_uptime'),
                'value' => '99.9%',
            ],
        ];

        $this->primaryCta = [
            'label' => trans('ui.home.ctas.primary'),
            'href' => route('browse', ['locale' => $locale]),
            'icon' => 'play',
        ];

        $this->secondaryCta = [
            'label' => trans('ui.home.ctas.secondary'),
            'href' => route('pricing', ['locale' => $locale]),
            'icon' => 'sparkles',
        ];

        $this->lastUpdatedLabel = trans('ui.home.last_updated', [
            'date' => Carbon::now()->format('M j, Y'),
        ]);
    }

    public function render(): View
    {
        return view('pages.home')
            ->layout('layouts.app', [
                'title' => trans('ui.home.title'),
            ]);
    }
}
