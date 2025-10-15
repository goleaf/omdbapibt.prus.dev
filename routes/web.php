<?php

use App\Http\Controllers\BillingPortalController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\Webhooks\StripeWebhookController;
use App\Livewire\Admin\HorizonMonitor;
use App\Livewire\Admin\ParserModerationDashboard;
use App\Livewire\TvShowDetail;
use Illuminate\Support\Facades\Route;
use Laravel\Cashier\Http\Middleware\VerifyWebhookSignature;

$availableLocales = config('translatable.locales', []);

if ($availableLocales === []) {
    $availableLocales = [config('app.fallback_locale', 'en')];
}

$registerAppRoutes = function (): void {
    Route::view('/', 'pages.home')->name('home');
    Route::view('/browse', 'pages.browse')->name('browse');
    Route::view('/pricing', 'pages.pricing')->name('pricing');

    Route::get('/movies/{slug}', fn (string $slug) => view('pages.movies.show', ['slug' => $slug]))
        ->name('movies.show');

    Route::get('/shows/{slug}', fn (string $slug) => view('pages.shows.show', ['slug' => $slug]))
        ->name('shows.show');

    Route::get('/tv/{show}', TvShowDetail::class)
        ->name('tv.show');

    Route::middleware('auth')->group(function (): void {
        Route::view('/dashboard', 'dashboard')->name('dashboard');
        Route::view('/account', 'pages.account')->name('account');

        Route::get('/billing/portal', BillingPortalController::class)
            ->name('billing.portal');

        Route::post('/subscriptions', [SubscriptionController::class, 'store'])
            ->name('subscriptions.store');
    });

    Route::middleware(['auth', 'admin'])->group(function (): void {
        Route::get('/admin/horizon-monitor', HorizonMonitor::class)
            ->name('admin.horizon-monitor');
        Route::get('/admin/parser-moderation', ParserModerationDashboard::class)
            ->name('admin.parser-moderation');
    });
};

Route::middleware('set-locale')->group(function () use ($availableLocales, $registerAppRoutes): void {
    $registerAppRoutes();

    Route::prefix('{locale}')
        ->whereIn('locale', $availableLocales)
        ->name('localized.')
        ->group(function () use ($registerAppRoutes): void {
            $registerAppRoutes();
        });
});

Route::post('/stripe/webhook', [StripeWebhookController::class, 'handleWebhook'])
    ->middleware(VerifyWebhookSignature::class)
    ->name('webhooks.stripe');
