<?php

namespace Tests\Feature\Livewire;

use App\Livewire\PricingPage;
use Illuminate\Support\Collection;
use Livewire\Livewire;
use Tests\TestCase;

class PricingPageTest extends TestCase
{
    public function test_pricing_page_exposes_expected_plan_collection(): void
    {
        Livewire::test(PricingPage::class)
            ->assertViewHas('plans', function ($plans) {
                $this->assertInstanceOf(Collection::class, $plans);
                $this->assertCount(3, $plans);

                $planNames = $plans->pluck('name')->all();
                $this->assertSame(['Free', 'Premium Monthly', 'Premium Yearly'], $planNames);

                $premiumMonthly = $plans->firstWhere('name', 'Premium Monthly');
                $this->assertNotNull($premiumMonthly);
                $this->assertSame('Start 7-day trial', $premiumMonthly['cta']['label']);
                $this->assertStringContainsString('bg-emerald-500', $premiumMonthly['cta']['classes']);
                $this->assertStringContainsString('border-emerald-500/80', $premiumMonthly['card_classes']);

                $freePlan = $plans->firstWhere('name', 'Free');
                $this->assertNotNull($freePlan);
                $this->assertSame('Great for exploring the platform and staying in the loop.', $freePlan['description']);
                $this->assertStringContainsString('border-slate-800', $freePlan['card_classes']);

                return true;
            });
    }

    public function test_pricing_page_renders_plan_copy(): void
    {
        Livewire::test(PricingPage::class)
            ->assertSeeInOrder([
                'Free',
                '$0',
                'Create free account',
                'Premium Monthly',
                '$9.99',
                'Start 7-day trial',
                'Premium Yearly',
                '$99.99',
                'Switch to yearly',
            ]);
    }
}
