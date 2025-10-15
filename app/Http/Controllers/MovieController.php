<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class MovieController extends Controller
{
    public function show(Request $request, Movie $movie): View|RedirectResponse
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('signup');
        }

        Gate::forUser($user)->authorize('view', $movie);

        return view('pages.movies.show', [
            'slug' => $movie->slug,
            'movie' => $movie,
        ]);
    }
}
