<?php

namespace App\Services\External;

class OmdbClient extends ExternalApiClient
{
    public function __construct()
    {
        parent::__construct(config('services.omdb', []));
    }
}
