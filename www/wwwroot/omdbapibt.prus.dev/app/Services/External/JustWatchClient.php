<?php

namespace App\Services\External;

class JustWatchClient extends ExternalApiClient
{
    public function __construct()
    {
        parent::__construct(config('services.justwatch', []));
    }
}
