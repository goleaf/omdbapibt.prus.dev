<?php

namespace App\Support;

use Illuminate\Cache\ArrayStore;

class RedisStubStore extends ArrayStore
{
    public function __construct()
    {
        parent::__construct();

        $this->setPrefix('redis_stub:');
    }
}
