<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\URL;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Ensure locale route parameter has a default in tests.
        URL::defaults(['locale' => config('app.fallback_locale', 'en')]);
    }
}
