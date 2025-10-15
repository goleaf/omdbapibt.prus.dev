<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
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
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
