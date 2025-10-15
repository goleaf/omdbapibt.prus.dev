<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (empty(config('app.key'))) {
            config([
                'app.key' => 'base64:'.base64_encode('0123456789abcdef0123456789abcdef'),
            ]);
        }
    }
}
