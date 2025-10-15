<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class BrowseController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();

        if ($user && $user->hasPremiumAccess()) {
            return view('pages.browse');
        }

        return view('pages.browse-locked');
    }
}
