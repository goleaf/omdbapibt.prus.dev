<?php

namespace App\Http\Controllers\Impersonation;

use App\Support\ImpersonationManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class StopImpersonationController
{
    public function __invoke(Request $request, ImpersonationManager $impersonationManager): RedirectResponse
    {
        $isImpersonating = $impersonationManager->isImpersonating();

        $impersonationManager->stop($request->user(), $request->user());

        $redirect = redirect()->back(fallback: route('home', ['locale' => app()->getLocale()]));

        if ($isImpersonating) {
            return $redirect->with('status', 'Impersonation session ended.');
        }

        return $redirect;
    }
}
