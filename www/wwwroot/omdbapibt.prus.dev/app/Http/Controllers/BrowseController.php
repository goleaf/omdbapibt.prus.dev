<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use Illuminate\Contracts\View\View;

class BrowseController extends Controller
{
    /**
     * Display the catalog of available movies.
     */
    public function __invoke(): View
    {
        $movies = Movie::orderByDesc('popularity')->take(20)->get();

        return view('browse', [
            'movies' => $movies,
        ]);
    }
}
