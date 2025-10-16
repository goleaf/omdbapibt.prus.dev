<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        if (! config('app.key')) {
            config()->set('app.key', 'base64:'.base64_encode(random_bytes(32)));
        }

        $manifestDirectory = public_path('build');
        $manifestPath = $manifestDirectory.'/manifest.json';

        if (! is_dir($manifestDirectory)) {
            mkdir($manifestDirectory, 0755, true);
        }

        file_put_contents($manifestPath, json_encode([
            'resources/js/app.js' => [
                'file' => 'assets/app.js',
                'src' => 'resources/js/app.js',
                'isEntry' => true,
                'css' => ['assets/app.css'],
            ],
            'resources/css/app.css' => [
                'file' => 'assets/app.css',
                'src' => 'resources/css/app.css',
            ],
        ], JSON_PRETTY_PRINT));

        /** @var \Illuminate\Routing\Router $router */
        $router = app('router');

        if (! $router->has('login')) {
            $router->get('/login', fn () => '')->name('login');
        }

        if (! $router->has('register')) {
            $router->get('/register', fn () => '')->name('register');
        }

        if (! $router->has('logout')) {
            $router->post('/logout', fn () => redirect('/'))->name('logout');
        }

        $router->getRoutes()->refreshNameLookups();
    }

    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $defaultLocale = config('translatable.fallback_locale', config('app.fallback_locale', 'en'));

        $this->get('/')
            ->assertRedirect("/{$defaultLocale}");

        $this->followingRedirects()
            ->get('/')
            ->assertOk();
    }

    public function test_footer_links_render_from_configuration(): void
    {
        config()->set('site.footer.links', [
            [
                'label' => 'ui.nav.footer.terms',
                'url' => 'https://example.com/terms',
            ],
            [
                'label' => 'ui.nav.footer.support',
                'route' => 'login',
                'target' => '_blank',
                'rel' => 'noopener',
            ],
        ]);

        $response = $this->followingRedirects()->get('/');

        $response
            ->assertSee(__('ui.nav.footer.terms'), false)
            ->assertSee('https://example.com/terms', false)
            ->assertSee(localized_route('login'), false)
            ->assertSee('_blank', false)
            ->assertSee('noopener', false);
    }
}
