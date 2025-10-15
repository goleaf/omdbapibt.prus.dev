<?php

namespace App\Livewire;

use Livewire\Attributes\Computed;
use Livewire\Component;

class AccountSettingsSummary extends Component
{
    #[Computed]
    public function subscription(): array
    {
        return [
            'plan' => 'Premium Monthly',
            'status' => 'Active',
            'renewal_date' => now()->addMonth()->format('F j, Y'),
            'price' => '$9.99',
            'benefits' => [
                'Unlimited full-detail access to movies and TV shows',
                'Personalized watchlist and recommendations',
                '4K streaming on supported devices',
                'Priority support and release alerts',
            ],
        ];
    }

    #[Computed]
    public function preferences(): array
    {
        return [
            ['label' => 'Preferred language', 'value' => 'English'],
            ['label' => 'Maturity filter', 'value' => 'Off'],
            ['label' => 'Autoplay trailers', 'value' => 'Enabled'],
            ['label' => 'Email alerts', 'value' => 'Weekly digest'],
        ];
    }

    public function render()
    {
        return view('livewire.account-settings-summary');
    }
}
