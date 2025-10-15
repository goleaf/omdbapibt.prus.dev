<?php

namespace Tests\Feature;

use Illuminate\Support\Str;
use Tests\TestCase;

class RedisSessionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('session.driver', 'redis');
        config()->set('session.connection', 'default');
    }

    public function test_session_data_is_persisted_in_redis(): void
    {
        $session = app('session')->driver('redis');
        $session->start();

        $key = 'redis_session_test_'.Str::random(8);
        $value = Str::random(12);

        $session->put($key, $value);
        $session->save();

        $sessionId = $session->getId();
        $handler = $session->getHandler();

        $raw = $handler->read($sessionId);

        $this->assertNotEmpty($raw, 'Session payload should be stored in Redis.');

        $payload = unserialize($raw, ['allowed_classes' => true]);

        $this->assertSame($value, $payload[$key] ?? null, 'Session data should be retrievable from Redis.');

        $handler->destroy($sessionId);
    }
}
