<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

class StaticPageControllerTest extends TestCase
{
    public function test_terms_page_handles_non_iterable_sections(): void
    {
        $this->get('/en/terms')
            ->assertOk()
            ->assertSee(trans('ui.pages.terms.intro'));
    }

    public function test_support_page_applies_default_cta_fallbacks(): void
    {
        $this->get('/en/support')
            ->assertOk()
            ->assertSee(trans('ui.pages.support.intro'))
            ->assertSee(trans('ui.pages.support.default_cta'));
    }
}
