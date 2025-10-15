<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function __invoke(Request $request): RedirectResponse|View
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        if ($user->hasPremiumAccess()) {
            return redirect()->route('browse')->with('status', __('You already have an active subscription.'));
        }

        $plans = array_filter([
            'monthly' => config('services.stripe.prices.monthly'),
            'yearly' => config('services.stripe.prices.yearly'),
        ]);

        return view('pages.checkout', [
            'plans' => $plans,
        ]);
    }
}
