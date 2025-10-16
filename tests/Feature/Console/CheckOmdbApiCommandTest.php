<?php

namespace Tests\Feature\Console;

use App\Console\Commands\CheckOmdbApi;
use App\Models\OmdbApiKey;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use ReflectionClass;
use Symfony\Component\Console\Input\ArrayInput;
use Tests\TestCase;

class CheckOmdbApiCommandTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Cache::forget('commands:check-omdb-api:checkpoint');
    }

    public function test_command_uses_configuration_defaults(): void
    {
        config([
            'services.omdb.base_url' => 'https://omdb.test/api',
            'services.omdb.validation.batch_size' => 2,
            'services.omdb.validation.test_imdb_id' => 'tt1234567',
            'services.omdb.validation.timeout' => 15,
        ]);

        OmdbApiKey::factory()->count(2)->create([
            'status' => OmdbApiKey::STATUS_PENDING,
        ]);

        $captured = [];

        Http::fake(function (Request $request) use (&$captured) {
            $captured[] = $request;

            return Http::response([
                'Response' => 'False',
                'Error' => 'Invalid API key!',
            ], 200);
        });

        $this->artisan('checkapi')
            ->assertSuccessful();

        $this->assertCount(2, $captured);

        foreach ($captured as $request) {
            $this->assertStringStartsWith('https://omdb.test/api', $request->url());
            $this->assertSame('tt1234567', $request->data()['i'] ?? null);
        }

        $command = $this->makeCommand([]);

        $this->assertSame(15, $this->invokeProtectedMethod($command, 'determineTimeout'));

        $this->assertSame(
            [OmdbApiKey::STATUS_INVALID, OmdbApiKey::STATUS_INVALID],
            OmdbApiKey::orderBy('id')->pluck('status')->all()
        );
    }

    public function test_cli_options_override_configuration(): void
    {
        config([
            'services.omdb.base_url' => 'https://omdb.test/api',
            'services.omdb.validation.batch_size' => 3,
            'services.omdb.validation.test_imdb_id' => 'tt0000001',
            'services.omdb.validation.timeout' => 5,
        ]);

        $first = OmdbApiKey::factory()->create([
            'status' => OmdbApiKey::STATUS_PENDING,
        ]);

        $second = OmdbApiKey::factory()->create([
            'status' => OmdbApiKey::STATUS_PENDING,
        ]);

        $captured = [];

        Http::fake(function (Request $request) use (&$captured) {
            $captured[] = $request;

            return Http::response([
                'Response' => 'False',
                'Error' => 'Invalid API key!',
            ], 200);
        });

        $queries = [];

        DB::listen(function ($query) use (&$queries): void {
            $queries[] = $query->sql;
        });

        $this->artisan('checkapi', [
            '--batch' => 1,
            '--timeout' => 3,
            '--imdb' => 'tt7654321',
        ])
            ->expectsOutputToContain(sprintf(
                'Processing 1 key(s) spanning primary keys %d through %d.',
                $first->getKey(),
                $first->getKey()
            ))
            ->expectsOutputToContain(sprintf(
                'Processing 1 key(s) spanning primary keys %d through %d.',
                $second->getKey(),
                $second->getKey()
            ))
            ->assertSuccessful();

        $this->assertCount(2, $captured);

        foreach ($captured as $request) {
            $this->assertSame('tt7654321', $request->data()['i'] ?? null);
        }

        $this->assertTrue(
            collect($queries)->contains(fn (string $sql) => str_contains(strtolower($sql), 'limit 1'))
        );

        $command = $this->makeCommand(['--timeout' => 3, '--batch' => 1, '--imdb' => 'tt7654321']);

        $this->assertSame(3, $this->invokeProtectedMethod($command, 'determineTimeout'));
        $this->assertSame(1, $this->invokeProtectedMethod($command, 'determineBatchSize'));
        $this->assertSame('tt7654321', $this->invokeProtectedMethod($command, 'determineTestImdbId'));
    }

    protected function makeCommand(array $options): CheckOmdbApi
    {
        $command = app(CheckOmdbApi::class);

        $command->setLaravel($this->app);

        $input = new ArrayInput($options, $command->getDefinition());

        $command->setInput($input);

        return $command;
    }

    protected function invokeProtectedMethod(object $object, string $method): mixed
    {
        $reflection = new ReflectionClass($object);
        $methodReflection = $reflection->getMethod($method);
        $methodReflection->setAccessible(true);

        return $methodReflection->invoke($object);
    }
}
