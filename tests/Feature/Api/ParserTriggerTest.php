<?php

namespace Tests\Feature\Api;

use App\Enums\ParserWorkload;
use App\Jobs\Parsing\ExecuteParserPipeline;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ParserTriggerTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_admin_users_cannot_trigger_parser(): void
    {
        config(['parser.queue' => 'parsing']);
        Queue::fake();

        $user = User::factory()->create();
        $authHeader = 'Basic '.base64_encode($user->email.':password');

        $this->withHeader('Authorization', $authHeader)
            ->postJson(route('api.parser.trigger'), ['workload' => ParserWorkload::Movies->value])
            ->assertForbidden();

        Queue::assertNothingPushed();
    }

    public function test_admin_users_receive_standardized_response_payload(): void
    {
        config(['parser.queue' => 'priority-parsing']);
        Queue::fake();

        $admin = User::factory()->admin()->create();
        $authHeader = 'Basic '.base64_encode($admin->email.':password');

        $response = $this->withHeader('Authorization', $authHeader)
            ->postJson(route('api.parser.trigger'), ['workload' => ParserWorkload::Movies->value]);

        $response
            ->assertAccepted()
            ->assertJsonPath('data.status', 'queued')
            ->assertJsonPath('data.workload', ParserWorkload::Movies->value)
            ->assertJsonPath('meta.queue', 'priority-parsing');

        Queue::assertPushed(ExecuteParserPipeline::class, function (ExecuteParserPipeline $job): bool {
            return $job->workload === ParserWorkload::Movies && $job->queue === 'priority-parsing';
        });
    }

    public function test_admin_users_can_trigger_parser_in_alternate_locale(): void
    {
        config(['parser.queue' => 'french-parsing']);
        Queue::fake();

        $admin = User::factory()->admin()->create();
        $authHeader = 'Basic '.base64_encode($admin->email.':password');

        app()->setLocale('fr');

        $response = $this->withHeader('Authorization', $authHeader)
            ->postJson(route('api.parser.trigger'), ['workload' => ParserWorkload::People->value]);

        $response
            ->assertAccepted()
            ->assertJsonPath('data.status', 'queued')
            ->assertJsonPath('data.workload', ParserWorkload::People->value)
            ->assertJsonPath('meta.queue', 'french-parsing');

        Queue::assertPushed(ExecuteParserPipeline::class, function (ExecuteParserPipeline $job): bool {
            return $job->workload === ParserWorkload::People && $job->queue === 'french-parsing';
        });
    }

    public function test_it_returns_english_validation_error_for_invalid_workload(): void
    {
        config(['parser.queue' => 'parsing']);
        Queue::fake();

        $admin = User::factory()->admin()->create();
        $authHeader = 'Basic '.base64_encode($admin->email.':password');

        app()->setLocale('en');

        $expectedMessage = __('validation.custom.workload.enum', [], 'en');

        $response = $this->withHeader('Authorization', $authHeader)
            ->postJson(route('api.parser.trigger'), ['workload' => 'invalid']);

        $response->assertUnprocessable();
        $response->assertExactJson([
            'message' => $expectedMessage,
            'errors' => [
                'workload' => [$expectedMessage],
            ],
        ]);

        Queue::assertNothingPushed();
    }

    #[DataProvider('localizedValidationErrorProvider')]
    public function test_it_localizes_validation_error_messages(string $locale): void
    {
        config(['parser.queue' => 'parsing']);
        Queue::fake();

        $admin = User::factory()->admin()->create();
        $authHeader = 'Basic '.base64_encode($admin->email.':password');

        app()->setLocale($locale);

        $expectedMessage = __('validation.custom.workload.enum');

        $response = $this->withHeader('Authorization', $authHeader)
            ->postJson(route('api.parser.trigger'), ['workload' => 'invalid']);

        $response->assertUnprocessable();
        $response->assertExactJson([
            'message' => $expectedMessage,
            'errors' => [
                'workload' => [$expectedMessage],
            ],
        ]);

        Queue::assertNothingPushed();
    }

    /**
     * @return array<string, array{0: string}>
     */
    public static function localizedValidationErrorProvider(): array
    {
        return [
            'spanish' => ['es'],
            'french' => ['fr'],
        ];
    }
}
