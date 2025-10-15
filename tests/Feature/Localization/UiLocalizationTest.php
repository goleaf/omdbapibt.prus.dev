<?php

namespace Tests\Feature\Localization;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UiLocalizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_navigation_translations_are_available(): void
    {
        $this->assertSame('Explorar', trans('ui.nav.links.browse', [], 'es'));
        $this->assertSame('Tarifs', trans('ui.nav.links.pricing', [], 'fr'));
        $this->assertSame('Únete ahora', trans('ui.nav.auth.register', [], 'es'));
    }

    public function test_dashboard_translations_are_available(): void
    {
        $this->assertSame('Lancez votre essai gratuit de 7 jours.', trans('ui.dashboard.trial.intro_title', ['days' => 7], 'fr'));
        $this->assertSame('Cancela en cualquier momento antes de que finalice la prueba para evitar cargos.', trans('ui.dashboard.trial.cancel_notice', [], 'es'));
    }
}
