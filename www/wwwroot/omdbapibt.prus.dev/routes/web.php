<?php

use App\Http\Controllers\BillingPortalController;
use App\Http\Controllers\Webhooks\StripeWebhookController;
use Illuminate\Support\Facades\Route;
use Laravel\Cashier\Http\Middleware\VerifyWebhookSignature;

$availableLocales = config('translatable.locales', []);

if ($availableLocales === []) {
    $availableLocales = [config('app.fallback_locale', 'en')];
}

$localizedRoutes = function (): void {
    Route::view('/', 'welcome')->name('localized.home');

    Route::middleware('auth')->group(function (): void {
        Route::view('/dashboard', 'dashboard')->name('localized.dashboard');

        Route::get('/billing/portal', BillingPortalController::class)
            ->name('localized.billing.portal');
    });
};

Route::middleware('set-locale')->group(function () use ($localizedRoutes): void {
    Route::view('/', 'welcome')->name('home');

    Route::middleware('auth')->group(function (): void {
        Route::view('/dashboard', 'dashboard')->name('dashboard');

        Route::get('/billing/portal', BillingPortalController::class)
            ->name('billing.portal');
    });

    Route::prefix('{locale}')
        ->whereIn('locale', $availableLocales)
        ->group($localizedRoutes);
});

Route::post('/stripe/webhook', [StripeWebhookController::class, 'handleWebhook'])
    ->middleware(VerifyWebhookSignature::class)
    ->name('webhooks.stripe');
