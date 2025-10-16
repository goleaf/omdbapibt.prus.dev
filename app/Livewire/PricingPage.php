<?php

namespace App\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Component;

class PricingPage extends Component
{
    public function render(): View
    {
        $plans = $this->plans();

        return view('pages.pricing', [
            'plans' => $plans,
        ]);
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    private function plans(): Collection
    {
        return collect([
            $this->buildPlan(
                name: 'Free',
                price: '$0',
                frequency: 'forever',
                ctaLabel: 'Create free account',
                highlighted: false,
                features: [
                    'Browse trending movies and shows',
                    'Limited metadata (title, year, synopsis)',
                    'Save up to 10 watchlist items',
                    'Community reviews preview',
                ],
            ),
            $this->buildPlan(
                name: 'Premium Monthly',
                price: '$9.99',
                frequency: 'per month',
                ctaLabel: 'Start 7-day trial',
                highlighted: true,
                features: [
                    'Full metadata (cast, crew, streaming, trailers)',
                    'Unlimited watchlist and history tracking',
                    'Personalized recommendations',
                    'Priority support and release alerts',
                ],
            ),
            $this->buildPlan(
                name: 'Premium Yearly',
                price: '$99.99',
                frequency: 'per year',
                ctaLabel: 'Switch to yearly',
                highlighted: false,
                features: [
                    'All premium monthly features',
                    'Two bonus guest passes',
                    'Exclusive festival coverage',
                    'Save 17% compared to monthly',
                ],
            ),
        ]);
    }

    /**
     * @param  array<int, string>  $features
     * @return array<string, mixed>
     */
    private function buildPlan(string $name, string $price, string $frequency, string $ctaLabel, bool $highlighted, array $features): array
    {
        return [
            'name' => $name,
            'price' => $price,
            'frequency' => $frequency,
            'description' => $this->planDescription($highlighted),
            'cta' => [
                'label' => $ctaLabel,
                'classes' => $this->planCtaClasses($highlighted),
            ],
            'card_classes' => $this->planCardClasses($highlighted),
            'features' => $features,
        ];
    }

    private function planDescription(bool $highlighted): string
    {
        if ($highlighted) {
            return 'Best for superfans and power researchers.';
        }

        return 'Great for exploring the platform and staying in the loop.';
    }

    private function planCardClasses(bool $highlighted): string
    {
        $base = 'rounded-3xl border bg-slate-900/70 p-8 shadow-lg shadow-slate-950/30';

        if ($highlighted) {
            return $base.' border-emerald-500/80 ring-4 ring-emerald-500/20';
        }

        return $base.' border-slate-800';
    }

    private function planCtaClasses(bool $highlighted): string
    {
        $base = 'mt-6 inline-flex w-full justify-center rounded-full px-5 py-3 text-sm font-semibold transition';

        if ($highlighted) {
            return $base.' bg-emerald-500 text-emerald-950 hover:bg-emerald-400';
        }

        return $base.' border border-slate-700 text-slate-200 hover:border-emerald-400 hover:text-emerald-200';
    }
}
