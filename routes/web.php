<?php

use App\Http\Controllers\BillingPortalController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\Webhooks\StripeWebhookController;
use App\Livewire\Admin\HorizonMonitor;
use App\Livewire\TvShowDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Laravel\Cashier\Http\Middleware\VerifyWebhookSignature;

$availableLocales = config('translatable.locales', []);

if ($availableLocales === []) {
    $availableLocales = [config('app.fallback_locale', 'en')];
}

$registerAppRoutes = function (): void {
    Route::view('/', 'pages.home')->name('home');
    Route::view('/pricing', 'pages.pricing')->name('pricing');

    Route::middleware('guest')->group(function (): void {
        Route::view('/signup', 'pages.signup')->name('signup');
    });

    Route::middleware('subscription.access')->group(function (): void {
        Route::view('/browse', 'pages.browse')->name('browse');

        Route::get('/movies/{movie:slug}', [MovieController::class, 'show'])
            ->name('movies.show');

        Route::get('/shows/{slug}', fn (string $slug) => view('pages.shows.show', ['slug' => $slug]))
            ->name('shows.show');

        Route::get('/tv/{show}', TvShowDetail::class)
            ->name('tv.show');
    });

    Route::get('/subscriptions/checkout', [SubscriptionController::class, 'create'])
        ->name('subscriptions.checkout');

    Route::middleware('auth')->group(function (): void {
        Route::view('/dashboard', 'dashboard')->name('dashboard');
        Route::view('/account', 'pages.account')->name('account');

        Route::post('/logout', function (Request $request) {
            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('home');
        })->name('logout');

        Route::get('/billing/portal', BillingPortalController::class)
            ->name('billing.portal');

        Route::post('/subscriptions', [SubscriptionController::class, 'store'])
            ->name('subscriptions.store');
    });

    Route::middleware(['auth', 'admin'])->group(function (): void {
        Route::get('/admin/horizon-monitor', HorizonMonitor::class)
            ->name('admin.horizon-monitor');
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
