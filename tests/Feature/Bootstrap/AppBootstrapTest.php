<?php

namespace Tests\Feature\Bootstrap;

use PHPUnit\Framework\TestCase;

class AppBootstrapTest extends TestCase
{
    public function test_app_key_is_generated_during_bootstrap_when_missing(): void
    {
        $basePath = dirname(__DIR__, 3);

        $originalServerKey = $_SERVER['APP_KEY'] ?? null;
        $originalEnvKey = $_ENV['APP_KEY'] ?? null;
        $originalPutEnv = getenv('APP_KEY');

        putenv('APP_KEY');
        unset($_SERVER['APP_KEY'], $_ENV['APP_KEY']);

        $app = require $basePath.'/bootstrap/app.php';

        try {
            $this->assertNotEmpty($_ENV['APP_KEY']);
            $this->assertSame($_ENV['APP_KEY'], $_SERVER['APP_KEY']);
            $this->assertSame($_ENV['APP_KEY'], getenv('APP_KEY'));
        } finally {
            if ($originalServerKey !== null) {
                $_SERVER['APP_KEY'] = $originalServerKey;
            } else {
                unset($_SERVER['APP_KEY']);
            }

            if ($originalEnvKey !== null) {
                $_ENV['APP_KEY'] = $originalEnvKey;
            } else {
                unset($_ENV['APP_KEY']);
            }

            if ($originalPutEnv !== false) {
                putenv('APP_KEY='.$originalPutEnv);
            } else {
                putenv('APP_KEY');
            }

            unset($app);
        }
    }
}
