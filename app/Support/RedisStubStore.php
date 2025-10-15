<?php

namespace App\Support;

use Illuminate\Cache\ArrayStore;

class RedisStubStore extends ArrayStore
{
    /**
     * The stubbed prefix applied to cached keys.
     */
    protected string $prefix = 'redis_stub:';

    public function __construct()
    {
        parent::__construct();
    }

    public function getPrefix(): string
    {
        return $this->prefix;
    }
}
