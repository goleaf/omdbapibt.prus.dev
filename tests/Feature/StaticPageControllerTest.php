<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Support\Facades\Lang;
use Tests\TestCase;

class StaticPageControllerTest extends TestCase
{
    public function test_terms_page_handles_non_iterable_sections(): void
    {
        Lang::addLines([
            'pages' => [
                'terms' => [
                    'sections' => 'not-an-array',
                ],
            ],
        ], 'en', 'ui');

        $this->get('/en/terms')
            ->assertOk()
            ->assertSee(trans('ui.pages.terms.intro'));
    }

    public function test_support_page_applies_default_cta_fallbacks(): void
    {
        Lang::addLines([
            'pages' => [
                'support' => [
                    'sections' => [
                        [
                            'title' => 'Need help?',
                            'paragraphs' => 'Reach out any time.',
                            'items' => 'Email support',
                            'cta' => [],
                        ],
                    ],
                ],
            ],
        ], 'en', 'ui');

        $this->get('/en/support')
            ->assertOk()
            ->assertSee('Need help?')
            ->assertSee('Reach out any time.')
            ->assertSee('Email support')
            ->assertSee('mailto:support@omdbstream.test')
            ->assertSee(trans('ui.pages.support.default_cta'));
    }
}

