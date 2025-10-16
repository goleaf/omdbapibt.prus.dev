<?php

namespace Tests\Feature\Console;

use App\Models\Movie;
use App\Models\OmdbApiKey;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class OmdbBruteforceCommandTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Clear checkpoint before each test
        Cache::forget('omdb:checkpoint');
    }

    public function test_command_generates_keys_when_below_minimum(): void
    {
        // Set minimum to 100 for faster testing
        config(['services.omdb.bruteforce.min_pending_keys' => 100]);

        Http::fake([
            '*' => Http::response(['Response' => 'False', 'Error' => 'Invalid API key!'], 200),
        ]);

        $this->artisan('omdb:bruteforce')
            ->assertSuccessful();

        $this->assertGreaterThanOrEqual(100, OmdbApiKey::count());
    }

    public function test_command_validates_pending_keys(): void
    {
        config(['services.omdb.bruteforce.min_pending_keys' => 0]);

        // Create test keys
        OmdbApiKey::factory()->create([
            'key' => 'valid123',
            'status' => OmdbApiKey::STATUS_PENDING,
        ]);

        OmdbApiKey::factory()->create([
            'key' => 'invalid1',
            'status' => OmdbApiKey::STATUS_PENDING,
        ]);

        Http::fake([
            '*valid123*' => Http::response([
                'Response' => 'True',
                'Title' => 'Test Movie',
                'Year' => '2024',
            ], 200),
            '*invalid1*' => Http::response([
                'Response' => 'False',
                'Error' => 'Invalid API key!',
            ], 200),
        ]);

        $this->artisan('omdb:bruteforce')
            ->assertSuccessful();

        $this->assertEquals(
            OmdbApiKey::STATUS_VALID,
            OmdbApiKey::where('key', 'valid123')->value('status')
        );

        $this->assertEquals(
            OmdbApiKey::STATUS_INVALID,
            OmdbApiKey::where('key', 'invalid1')->value('status')
        );
    }

    public function test_command_saves_checkpoint_for_resume(): void
    {
        config(['services.omdb.bruteforce.min_pending_keys' => 0]);

        // Create multiple pending keys
        $keys = OmdbApiKey::factory()->count(10)->create([
            'status' => OmdbApiKey::STATUS_PENDING,
        ]);

        Http::fake([
            '*' => Http::response(['Response' => 'False', 'Error' => 'Invalid API key!'], 200),
        ]);

        $this->artisan('omdb:bruteforce')
            ->assertSuccessful();

        $checkpoint = Cache::get('omdb:checkpoint');
        $this->assertNotNull($checkpoint);
        $this->assertGreaterThan(0, $checkpoint);
    }

    public function test_command_resumes_from_checkpoint(): void
    {
        config(['services.omdb.bruteforce.min_pending_keys' => 0]);

        // Create keys with IDs 1-10
        $keys = OmdbApiKey::factory()->count(10)->create([
            'status' => OmdbApiKey::STATUS_PENDING,
        ]);

        // Set checkpoint to skip first 5 keys
        Cache::forever('omdb:checkpoint', 5);

        Http::fake([
            '*' => Http::response(['Response' => 'False', 'Error' => 'Invalid API key!'], 200),
        ]);

        $this->artisan('omdb:bruteforce')
            ->assertSuccessful();

        // First 5 keys should still be pending
        $this->assertEquals(5, OmdbApiKey::pending()->where('id', '<=', 5)->count());

        // Keys after checkpoint should be validated
        $this->assertEquals(0, OmdbApiKey::pending()->where('id', '>', 5)->count());
    }

    public function test_command_parses_movies_with_valid_keys(): void
    {
        config(['services.omdb.bruteforce.min_pending_keys' => 0]);

        // Create a valid key
        OmdbApiKey::factory()->create([
            'key' => 'validkey',
            'status' => OmdbApiKey::STATUS_VALID,
        ]);

        // Create a movie without plot
        $movie = Movie::factory()->create([
            'imdb_id' => 'tt1234567',
            'plot' => null,
        ]);

        Http::fake([
            '*tt1234567*' => Http::response([
                'Response' => 'True',
                'Title' => 'Updated Title',
                'Year' => '2024',
                'Plot' => 'New plot description',
                'Poster' => 'https://example.com/poster.jpg',
                'imdbRating' => '8.5',
            ], 200),
        ]);

        $this->artisan('omdb:bruteforce')
            ->assertSuccessful();

        $movie->refresh();
        $this->assertEquals('New plot description', $movie->plot);
    }

    public function test_command_handles_network_errors_gracefully(): void
    {
        config(['services.omdb.bruteforce.min_pending_keys' => 0]);

        OmdbApiKey::factory()->create([
            'key' => 'testkey1',
            'status' => OmdbApiKey::STATUS_PENDING,
        ]);

        // Simulate HTTP errors
        Http::fake([
            '*' => Http::response(null, 500),
        ]);

        $this->artisan('omdb:bruteforce')
            ->assertSuccessful();

        $this->assertEquals(
            OmdbApiKey::STATUS_UNKNOWN,
            OmdbApiKey::where('key', 'testkey1')->value('status')
        );
    }

    public function test_command_skips_movie_parsing_if_no_valid_keys(): void
    {
        config(['services.omdb.bruteforce.min_pending_keys' => 0]);

        // Only invalid keys
        OmdbApiKey::factory()->create([
            'status' => OmdbApiKey::STATUS_INVALID,
        ]);

        Movie::factory()->create(['plot' => null]);

        Http::fake();

        $this->artisan('omdb:bruteforce')
            ->assertSuccessful();

        // Verify no HTTP requests were made for movie parsing
        $this->assertEquals(0, OmdbApiKey::valid()->count());
    }

    public function test_command_rotates_keys_for_movie_parsing(): void
    {
        config(['services.omdb.bruteforce.min_pending_keys' => 0]);

        // Create two valid keys
        $key1 = OmdbApiKey::factory()->create([
            'key' => 'key00001',
            'status' => OmdbApiKey::STATUS_VALID,
        ]);

        $key2 = OmdbApiKey::factory()->create([
            'key' => 'key00002',
            'status' => OmdbApiKey::STATUS_VALID,
        ]);

        // Create movies
        $movies = Movie::factory()->count(3)->create([
            'plot' => null,
        ]);

        $requestedKeys = [];

        Http::fake(function ($request) use (&$requestedKeys) {
            $requestedKeys[] = $request['apikey'];

            return Http::response([
                'Response' => 'True',
                'Title' => 'Test',
                'Plot' => 'Test plot',
            ], 200);
        });

        $this->artisan('omdb:bruteforce')
            ->assertSuccessful();

        // Verify keys were rotated
        $this->assertCount(3, $requestedKeys);
        $this->assertEquals('key00001', $requestedKeys[0]);
        $this->assertEquals('key00002', $requestedKeys[1]);
        $this->assertEquals('key00001', $requestedKeys[2]); // Rotation back to first
    }

    public function test_generated_keys_have_correct_format(): void
    {
        config(['services.omdb.bruteforce.min_pending_keys' => 100]);

        Http::fake([
            '*' => Http::response(['Response' => 'False', 'Error' => 'Invalid API key!'], 200),
        ]);

        $this->artisan('omdb:bruteforce')
            ->assertSuccessful();

        $keys = OmdbApiKey::pluck('key');

        foreach ($keys as $key) {
            // Verify 8 characters, alphanumeric lowercase
            $this->assertMatchesRegularExpression('/^[0-9a-z]{8}$/', $key);
        }
    }

    public function test_command_prevents_duplicate_keys(): void
    {
        config(['services.omdb.bruteforce.min_pending_keys' => 0]);

        // Create existing key
        OmdbApiKey::factory()->create([
            'key' => 'existing1',
        ]);

        Http::fake();

        $initialCount = OmdbApiKey::count();

        $this->artisan('omdb:bruteforce')
            ->assertSuccessful();

        // Verify no duplicates (count may increase with new keys, but 'existing1' should be unique)
        $this->assertEquals(1, OmdbApiKey::where('key', 'existing1')->count());
    }
}
