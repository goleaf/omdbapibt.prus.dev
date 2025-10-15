<?php

namespace App\Http\Responses\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;

class MovieLookupResponse
{
    public static function from(Collection $movies): JsonResponse
    {
        return response()->json([
            'data' => $movies,
        ]);
    }
}
