<?php

use App\Http\Controllers\BillingPortalController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\Webhooks\StripeWebhookController;
use App\Livewire\TvShowDetail;
use Illuminate\Support\Facades\Route;
use Laravel\Cashier\Http\Middleware\VerifyWebhookSignature;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/tv/{show}', TvShowDetail::class)
    ->name('tv.show');

Route::get('/{locale}/tv/{show}', TvShowDetail::class)
    ->where('locale', '[a-zA-Z]{2}')
    ->name('tv.show.localized');

Route::middleware('auth')->group(function () {
    Route::view('/dashboard', 'dashboard')->name('dashboard');

    Route::get('/billing/portal', BillingPortalController::class)
        ->name('billing.portal');

    Route::post('/subscriptions', [SubscriptionController::class, 'store'])
        ->name('subscriptions.store');
});

Route::post('/stripe/webhook', [StripeWebhookController::class, 'handleWebhook'])
    ->middleware(VerifyWebhookSignature::class)
    ->name('webhooks.stripe');
