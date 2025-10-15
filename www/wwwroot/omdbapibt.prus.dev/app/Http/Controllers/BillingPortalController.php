<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class BillingPortalController extends Controller
{
    /**
     * Redirect the authenticated user to the Stripe billing portal.
     */
    public function __invoke(Request $request): RedirectResponse
    {
        $user = $request->user();

        abort_unless($user, 403);

        $user->createOrGetStripeCustomer();

        $returnUrl = route('dashboard');

        return redirect()->away($user->billingPortalUrl($returnUrl));
    }
}
