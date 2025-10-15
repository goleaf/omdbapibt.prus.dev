<?php

namespace Tests\Unit\Support;

use App\Support\RedisStubStore;
use Tests\TestCase;

class RedisStubStoreTest extends TestCase
{
    public function test_it_sets_a_distinct_prefix(): void
    {
        $store = new RedisStubStore;

        $this->assertSame('redis_stub:', $store->getPrefix());
    }

    public function test_it_behaves_like_a_cache_store(): void
    {
        $store = new RedisStubStore;

        $store->put('greeting', 'hello world', 60);
        $this->assertSame('hello world', $store->get('greeting'));

        $this->assertTrue($store->forget('greeting'));
        $this->assertNull($store->get('greeting'));
    }
}
