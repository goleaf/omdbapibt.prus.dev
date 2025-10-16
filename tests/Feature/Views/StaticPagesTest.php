<?php

namespace Tests\Feature\Views;

use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class StaticPagesTest extends TestCase
{
    /**
     * @return array<string, array{route: string, translations: array<int, string>}>
     */
    public static function staticPageProvider(): array
    {
        return [
            'terms' => [
                'route' => 'terms',
                'translations' => [
                    'ui.pages.terms.intro',
                    'ui.pages.terms.sections.0.title',
                ],
            ],
            'privacy' => [
                'route' => 'privacy',
                'translations' => [
                    'ui.pages.privacy.intro',
                    'ui.pages.privacy.sections.0.items.0',
                ],
            ],
            'support' => [
                'route' => 'support',
                'translations' => [
                    'ui.pages.support.intro',
                    'ui.pages.support.sections.0.paragraphs.0',
                ],
            ],
        ];
    }

    #[DataProvider('staticPageProvider')]
    public function test_static_pages_render_for_each_locale(string $route, array $translations): void
    {
        $locales = config('translatable.locales', ['en']);

        foreach ($locales as $locale) {
            $response = $this->get(route($route, ['locale' => $locale]));

            $response->assertOk();

            foreach ($translations as $translationKey) {
                $response->assertSeeText(trans($translationKey, [], $locale));
            }
        }
    }
}
