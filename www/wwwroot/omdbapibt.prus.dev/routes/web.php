<?php

use App\Http\Controllers\BillingPortalController;
use App\Http\Controllers\Webhooks\StripeWebhookController;
use App\Livewire\Admin\UserDirectory;
use Illuminate\Support\Facades\Route;
use Laravel\Cashier\Http\Middleware\VerifyWebhookSignature;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {
    Route::view('/dashboard', 'dashboard')->name('dashboard');

    Route::get('/billing/portal', BillingPortalController::class)
        ->name('billing.portal');
});

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/users', UserDirectory::class)
        ->name('admin.users');
});

Route::post('/stripe/webhook', [StripeWebhookController::class, 'handleWebhook'])
    ->middleware(VerifyWebhookSignature::class)
    ->name('webhooks.stripe');
