<?php

namespace Tests\Feature\Livewire;

use App\Livewire\HomePage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Livewire\Livewire;
use Tests\TestCase;

class HomePageTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_renders_homepage_content(): void
    {
        Carbon::setTestNow('2024-05-01 12:00:00');

        try {
            Livewire::test(HomePage::class)
                ->assertStatus(200)
                ->assertSee('A mobile-first command center for your watchlists')
                ->assertSee('Start browsing')
                ->assertSee('View membership tiers')
                ->assertSee('Streaming regions')
                ->assertSee('Flux-enabled components')
                ->assertSee('Library uptime')
                ->assertSee('Updated May 1, 2024 across all catalog sources.');
        } finally {
            Carbon::setTestNow();
        }
    }
}
