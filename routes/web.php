<?php

use App\Http\Controllers\BillingPortalController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\Webhooks\StripeWebhookController;
use App\Livewire\Admin\AnalyticsDashboard;
use App\Livewire\Admin\HorizonMonitor;
use App\Livewire\Admin\ParserModerationDashboard;
use App\Livewire\TvShowDetail;
use App\Livewire\WatchHistoryBrowser;
use Illuminate\Support\Facades\Route;
use Laravel\Cashier\Http\Middleware\VerifyWebhookSignature;

$supportedLocales = config('translatable.locales', []);
$defaultLocale = config('translatable.fallback_locale', config('app.fallback_locale', 'en'));

if ($supportedLocales === []) {
    $supportedLocales = [$defaultLocale];
} elseif (! in_array($defaultLocale, $supportedLocales, true)) {
    $supportedLocales[] = $defaultLocale;
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

        Route::get('/account/watch-history', WatchHistoryBrowser::class)
            ->middleware('subscriber')
            ->name('account.watch-history');

        Route::get('/billing/portal', BillingPortalController::class)
            ->name('billing.portal');

        Route::post('/subscriptions', [SubscriptionController::class, 'store'])
            ->name('subscriptions.store');
    });

    Route::middleware(['auth', 'admin'])->group(function (): void {
        Route::get('/admin/analytics', AnalyticsDashboard::class)
            ->name('admin.analytics');
        Route::get('/admin/horizon-monitor', HorizonMonitor::class)
            ->name('admin.horizon-monitor');
        Route::get('/admin/parser-moderation', ParserModerationDashboard::class)
            ->name('admin.parser-moderation');
    });
};

Route::post('/stripe/webhook', [StripeWebhookController::class, 'handleWebhook'])
    ->middleware(VerifyWebhookSignature::class)
    ->name('webhooks.stripe');

Route::prefix('{locale}')
    ->middleware(['validate-locale', 'set-locale'])
    ->group(function () use ($registerAppRoutes): void {
        $registerAppRoutes();
    });

Route::get('/', function () use ($defaultLocale) {
    return redirect()->route('home', ['locale' => $defaultLocale]);
});

Route::fallback(function () use ($supportedLocales, $defaultLocale) {
    $firstSegment = request()->segment(1);

    if ($firstSegment && in_array($firstSegment, $supportedLocales, true)) {
        abort(404);
    }

    $path = trim(request()->path(), '/');
    $targetPath = $defaultLocale.($path !== '' ? '/'.$path : '');

    $redirect = redirect()->to('/'.$targetPath);

    if (! in_array(request()->method(), ['GET', 'HEAD', 'OPTIONS'], true)) {
        $redirect->setStatusCode(307);
    }

    return $redirect;
});
