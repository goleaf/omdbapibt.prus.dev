<?php

use App\Http\Controllers\BillingPortalController;
use App\Http\Controllers\Webhooks\StripeWebhookController;
use App\Livewire\MovieDetail;
use Illuminate\Support\Facades\Route;
use Laravel\Cashier\Http\Middleware\VerifyWebhookSignature;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('movies')->name('movies.')->group(function () {
    Route::get('/{movie}', MovieDetail::class)
        ->where('movie', '[A-Za-z0-9\-]+')
        ->name('show');
});

Route::middleware('auth')->group(function () {
    Route::view('/dashboard', 'dashboard')->name('dashboard');

    Route::get('/billing/portal', BillingPortalController::class)
        ->name('billing.portal');
});

Route::post('/stripe/webhook', [StripeWebhookController::class, 'handleWebhook'])
    ->middleware(VerifyWebhookSignature::class)
    ->name('webhooks.stripe');
