<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

class LocalizedAssetRouteTest extends TestCase
{
    protected function tearDown(): void
    {
        File::deleteDirectory(public_path('build/testing'));

        parent::tearDown();
    }

    public function test_localized_asset_request_serves_public_build_file(): void
    {
        $assetDirectory = public_path('build/testing');
        File::ensureDirectoryExists($assetDirectory);

        $filePath = $assetDirectory.'/example.txt';
        File::put($filePath, 'localized asset payload');

        $response = $this->get('/en/build/testing/example.txt');

        $response->assertOk();
        $cacheControl = $response->headers->get('Cache-Control');
        $this->assertNotNull($cacheControl);
        $this->assertStringContainsString('max-age=31536000', $cacheControl);
        $this->assertStringContainsString('public', $cacheControl);
        $this->assertStringContainsString('immutable', $cacheControl);
        $this->assertSame('localized asset payload', $response->getContent());
    }

    public function test_localized_asset_request_rejects_directory_traversal(): void
    {
        $this->get('/en/build/../.env')->assertNotFound();
    }
}
