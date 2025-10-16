<?php

use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\BillingPortalController;
use App\Http\Controllers\StopImpersonationController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\Webhooks\StripeWebhookController;
use App\Livewire\Admin\AnalyticsDashboard;
use App\Livewire\Admin\HorizonMonitor;
use App\Livewire\Admin\ParserModerationDashboard;
use App\Livewire\Admin\UiTranslationManager;
use App\Livewire\Admin\UserDirectory;
use App\Livewire\Auth\LoginForm;
use App\Livewire\Auth\SignupForm;
use App\Livewire\Browse\BrowsePage;
use App\Livewire\Checkout\PlanSelector;
use App\Livewire\PricingPage;
use App\Livewire\TvShowDetail;
use App\Livewire\WatchHistoryBrowser;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Laravel\Cashier\Http\Middleware\VerifyWebhookSignature;

$supportedLocales = config('translatable.locales', []);
$defaultLocale = config('translatable.fallback_locale', config('app.fallback_locale', 'en'));

if ($supportedLocales === []) {
    $supportedLocales = [$defaultLocale];
} elseif (! in_array($defaultLocale, $supportedLocales, true)) {
    $supportedLocales[] = $defaultLocale;
}

URL::defaults(['locale' => $defaultLocale]);

$registerAppRoutes = function (): void {
    Route::get('/', HomePage::class)->name('home');
    Route::get('/browse', BrowsePage::class)->name('browse');
    Route::get('/pricing', PricingPage::class)->name('pricing');
    Route::view('/ui/components', 'pages.ui.components')->name('ui.components');
    Route::get('/checkout', PlanSelector::class)->name('checkout');
    Route::get('/login', LoginForm::class)->name('login');
    Route::get('/signup', SignupForm::class)->name('signup');
    Route::get('/register', SignupForm::class)->name('register');

    Route::get('/movies/{movie}', fn (string $locale, string $movie) => view('pages.movies.show', ['movie' => $movie]))
        ->name('movies.show');

    Route::get('/shows/{slug}', fn (string $slug) => view('pages.shows.show', ['slug' => $slug]))
        ->name('shows.show');

    Route::get('/tv/{show}', TvShowDetail::class)
        ->name('tv.show');

    Route::get('/people/{person}', fn (string $locale, string $person) => view('pages.people.show', ['person' => $person]))
        ->name('people.show');

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

        Route::post('/logout', LogoutController::class)
            ->name('logout');

        Route::delete('/impersonation', StopImpersonationController::class)
            ->name('impersonation.stop');
    });

    Route::middleware(['auth', 'admin'])->group(function (): void {
        Route::get('/admin/analytics', AnalyticsDashboard::class)
            ->name('admin.analytics');
        Route::get('/admin/horizon-monitor', HorizonMonitor::class)
            ->name('admin.horizon-monitor');
        Route::get('/admin/parser-moderation', ParserModerationDashboard::class)
            ->name('admin.parser-moderation');
        Route::get('/admin/ui-translations', UiTranslationManager::class)
            ->name('admin.ui-translations');
        Route::get('/admin/users', UserDirectory::class)
            ->name('admin.users');
    });
};

Route::post('/stripe/webhook', [StripeWebhookController::class, 'handleWebhook'])
    ->middleware(VerifyWebhookSignature::class)
    ->name('webhooks.stripe');

Route::get('{locale}/build/{path}', function (string $locale, string $path) use ($supportedLocales) {
    if (! in_array($locale, $supportedLocales, true)) {
        abort(404);
    }

    if (str_contains($path, '..')) {
        abort(404);
    }

    $fullPath = public_path('build/'.$path);

    if (! File::exists($fullPath) || File::isDirectory($fullPath)) {
        abort(404);
    }

    return response(File::get($fullPath), 200, [
        'Content-Type' => File::mimeType($fullPath) ?: 'application/octet-stream',
        'Cache-Control' => 'public, max-age=31536000',
    ]);
})->where('path', '.*');

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
