<?php

namespace App\Http\Controllers;

use App\Support\ImpersonationManager;
use Illuminate\Http\RedirectResponse;

class StopImpersonationController extends Controller
{
    public function __invoke(ImpersonationManager $impersonationManager): RedirectResponse
    {
        if (! $impersonationManager->isImpersonating()) {
            return redirect()->to(localized_route('home'));
        }

        $impersonationManager->stop();

        return redirect()
            ->to(localized_route('admin.users'))
            ->with('status', __('ui.impersonation.stopped'));
    }
}
