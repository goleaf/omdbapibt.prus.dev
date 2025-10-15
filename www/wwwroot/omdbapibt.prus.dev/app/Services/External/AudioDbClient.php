<?php

namespace App\Services\External;

class AudioDbClient extends ExternalApiClient
{
    public function __construct()
    {
        parent::__construct(config('services.audiodb', []));
    }
}
