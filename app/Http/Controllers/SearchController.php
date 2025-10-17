<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('q', '');

        // TODO: Implement actual search logic
        // For now, return empty results
        $results = [
            'movies' => [],
            'shows' => [],
            'people' => [],
        ];

        return view('pages.search-results', [
            'query' => $query,
            'results' => $results,
        ]);
    }
}
