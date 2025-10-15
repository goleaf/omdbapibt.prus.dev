<?php

use App\Http\Controllers\Admin\UiTranslationController;
use App\Http\Controllers\BillingPortalController;
use App\Http\Controllers\Webhooks\StripeWebhookController;
use Illuminate\Support\Facades\Route;
use Laravel\Cashier\Http\Middleware\VerifyWebhookSignature;

Route::get('/', function () {
    return view('welcome');
});

Route::view('/login', 'auth.login')->name('login');

Route::middleware('auth')->group(function () {
    Route::view('/dashboard', 'dashboard')->name('dashboard');

    Route::get('/billing/portal', BillingPortalController::class)
        ->name('billing.portal');

    Route::prefix('admin')->name('admin.')->group(function () {
        Route::resource('translations', UiTranslationController::class)->except(['show']);
    });
});

Route::post('/stripe/webhook', [StripeWebhookController::class, 'handleWebhook'])
    ->middleware(VerifyWebhookSignature::class)
    ->name('webhooks.stripe');
