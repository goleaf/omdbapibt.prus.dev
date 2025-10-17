<?php

namespace Tests\Unit\Services;

use App\Models\Movie;
use App\Models\OmdbApiKey;
use App\Services\OmdbApiKeyManager;
use Illuminate\Cache\Repository as CacheRepository;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Factory as HttpFactory;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class OmdbApiKeyManagerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Cache::forget(OmdbApiKeyManager::VALIDATION_CHECKPOINT_CACHE_KEY);
    }

    public function test_generate_pending_keys_respects_minimum_threshold(): void
    {
        OmdbApiKey::factory()->count(2)->pending()->create();

        $manager = $this->makeManager(function (int $counter, int $length): string {
            return str_pad((string) $counter, $length, '0', STR_PAD_LEFT);
        });

        $generated = $manager->generatePendingKeys(5, '0123456789', 4, 2);

        $this->assertSame(3, $generated);
        $this->assertSame(5, OmdbApiKey::pending()->count());
    }

    public function test_validate_pending_keys_updates_status_and_checkpoint(): void
    {
        $first = OmdbApiKey::factory()->create(['status' => OmdbApiKey::STATUS_PENDING, 'key' => 'valid1']);
        $second = OmdbApiKey::factory()->create(['status' => OmdbApiKey::STATUS_PENDING, 'key' => 'invalid1']);
        $third = OmdbApiKey::factory()->create(['status' => OmdbApiKey::STATUS_PENDING, 'key' => 'unknown1']);

        Http::fake(function (Request $request) {
            $key = $request->data()['apikey'] ?? '';

            return match ($key) {
                'valid1' => Http::response(['Response' => 'True'], 200),
                'invalid1' => Http::response(['Response' => 'False', 'Error' => 'Invalid API Key!'], 200),
                default => Http::response('Server error', 500),
            };
        });

        $manager = $this->makeManager();

        $result = $manager->validatePendingKeys(5, 'tt1234567', 5, 'https://omdb.test/');

        $this->assertSame(3, $result['processed']);
        $this->assertSame(1, $result['valid']);
        $this->assertSame(1, $result['invalid']);
        $this->assertSame($third->getKey(), $result['checkpoint']);

        $this->assertEquals(
            [OmdbApiKey::STATUS_VALID, OmdbApiKey::STATUS_INVALID, OmdbApiKey::STATUS_UNKNOWN],
            OmdbApiKey::orderBy('id')->pluck('status')->all()
        );

        $this->assertSame(
            $third->getKey(),
            Cache::get(OmdbApiKeyManager::VALIDATION_CHECKPOINT_CACHE_KEY)
        );
    }

    public function test_import_from_remote_parses_json_and_skips_duplicates(): void
    {
        OmdbApiKey::factory()->create(['key' => 'existing1', 'status' => OmdbApiKey::STATUS_PENDING]);

        Http::fake(['https://remote.test/keys' => Http::response([
            'data' => ['new1', 'existing1', 'new2'],
        ], 200)]);

        $manager = $this->makeManager();

        $result = $manager->importFromRemote('https://remote.test/keys', 'data');

        $this->assertSame(['imported' => 2, 'skipped' => 1], $result);
        $this->assertEqualsCanonicalizing(['existing1', 'new1', 'new2'], OmdbApiKey::pluck('key')->all());
    }

    public function test_parse_movies_with_keys_rotates_keys_and_updates_movies(): void
    {
        Carbon::setTestNow(now());

        OmdbApiKey::factory()->create([
            'key' => 'key1',
            'status' => OmdbApiKey::STATUS_VALID,
            'last_confirmed_at' => now()->subMinute(),
        ]);

        OmdbApiKey::factory()->create([
            'key' => 'key2',
            'status' => OmdbApiKey::STATUS_VALID,
            'last_confirmed_at' => now()->subMinutes(2),
        ]);

        $movieOne = Movie::factory()->create([
            'imdb_id' => 'tt0000001',
            'plot' => null,
        ]);

        $movieTwo = Movie::factory()->create([
            'imdb_id' => 'tt0000002',
            'plot' => null,
        ]);

        $capturedKeys = [];

        Http::fake(function (Request $request) use (&$capturedKeys) {
            $capturedKeys[] = $request->data()['apikey'] ?? '';
            $imdb = $request->data()['i'] ?? '';

            if ($imdb === 'tt0000001') {
                return Http::response([
                    'Response' => 'True',
                    'Title' => 'Movie One',
                    'Plot' => 'Plot One',
                ], 200);
            }

            return Http::response([
                'Response' => 'True',
                'Title' => 'Movie Two',
                'Plot' => 'Plot Two',
            ], 200);
        });

        $manager = $this->makeManager();

        $result = $manager->parseMoviesWithKeys(10, 1, 'https://omdb.test/', 5);

        $this->assertSame(['processed' => 2, 'updated' => 2], $result);
        $titles = Movie::orderBy('id')->pluck('title')->map(function ($value) {
            if (is_array($value)) {
                return $value['en'] ?? null;
            }

            return $value;
        })->all();

        $this->assertEquals(['Movie One', 'Movie Two'], $titles);
        $this->assertEquals(['Plot One', 'Plot Two'], Movie::orderBy('id')->pluck('plot')->all());

        $this->assertEqualsCanonicalizing(['key1', 'key2'], $capturedKeys);

        Carbon::setTestNow();
    }

    protected function makeManager(?callable $generator = null): OmdbApiKeyManager
    {
        $http = app(HttpFactory::class);
        $cache = app(CacheRepository::class);
        $connection = app(ConnectionInterface::class);

        if ($generator === null) {
            return new OmdbApiKeyManager($http, $cache, $connection);
        }

        return new class($http, $cache, $connection, $generator) extends OmdbApiKeyManager
        {
            public function __construct(
                HttpFactory $http,
                CacheRepository $cache,
                ConnectionInterface $connection,
                protected $generator
            ) {
                parent::__construct($http, $cache, $connection);
            }

            protected int $counter = 0;

            protected function generateKey(string $charset, int $length): string
            {
                return ($this->generator)($this->counter++, $length);
            }
        };
    }
}
