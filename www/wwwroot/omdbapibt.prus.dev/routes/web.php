<?php

use App\Http\Controllers\BillingPortalController;
use App\Http\Controllers\Webhooks\StripeWebhookController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Laravel\Cashier\Http\Middleware\VerifyWebhookSignature;

Route::redirect('/', '/' . config('app.locale'));

Route::middleware('locale')->prefix('{locale}')->group(function () {
    Route::get('/', function () {
        return view('welcome');
    })->name('welcome');

    Route::middleware('auth')->group(function () {
        Route::view('/dashboard', 'dashboard')->name('dashboard');

        Route::get('/billing/portal', BillingPortalController::class)
            ->name('billing.portal');
    });
});

Route::post('/stripe/webhook', [StripeWebhookController::class, 'handleWebhook'])
    ->middleware(VerifyWebhookSignature::class)
    ->name('webhooks.stripe');

Route::fallback(function () {
    $request = request();

    if (! $request->isMethod('get') && ! $request->isMethod('head')) {
        abort(404);
    }

    $path = trim($request->path(), '/');

    if ($path === '') {
        abort(404);
    }

    $supportedLocales = config('app.supported_locales', [config('app.locale')]);
    $firstSegment = Str::before($path, '/');

    if (in_array($firstSegment, $supportedLocales, true)) {
        abort(404);
    }

    $defaultLocale = config('app.locale');
    $redirectTo = '/' . $defaultLocale . '/' . $path;

    if ($query = $request->getQueryString()) {
        $redirectTo .= '?' . $query;
    }

    return redirect()->to($redirectTo);
});
