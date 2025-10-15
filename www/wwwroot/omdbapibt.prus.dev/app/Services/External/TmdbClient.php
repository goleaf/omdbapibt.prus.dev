<?php

namespace App\Services\External;

class TmdbClient extends ExternalApiClient
{
    public function __construct()
    {
        parent::__construct(config('services.tmdb', []));
    }
}
