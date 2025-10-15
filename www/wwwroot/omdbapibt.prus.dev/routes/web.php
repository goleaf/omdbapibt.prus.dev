<?php

use App\Http\Controllers\Auth\SignupController;
use App\Http\Controllers\BillingPortalController;
use App\Http\Controllers\BrowseController;
use App\Http\Controllers\SubscriptionCheckoutController;
use App\Http\Controllers\Webhooks\StripeWebhookController;
use Illuminate\Support\Facades\Route;
use Laravel\Cashier\Http\Middleware\VerifyWebhookSignature;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/signup', SignupController::class)->name('signup');

Route::middleware('auth')->group(function () {
    Route::view('/dashboard', 'dashboard')->middleware('subscribed')->name('dashboard');

    Route::get('/billing/portal', BillingPortalController::class)
        ->name('billing.portal');

    Route::post('/subscribe', SubscriptionCheckoutController::class)
        ->name('subscription.checkout');

    Route::get('/browse', BrowseController::class)
        ->middleware('subscribed')
        ->name('browse');
});

Route::post('/stripe/webhook', [StripeWebhookController::class, 'handleWebhook'])
    ->middleware(VerifyWebhookSignature::class)
    ->name('webhooks.stripe');
