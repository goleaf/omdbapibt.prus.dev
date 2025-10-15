<?php

namespace Tests\Feature\Api;

use App\Enums\ParserWorkload;
use App\Jobs\Parsing\ExecuteParserPipeline;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
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

    public function test_admin_users_can_trigger_parser(): void
    {
        config(['parser.queue' => 'priority-parsing']);
        Queue::fake();

        $admin = User::factory()->admin()->create();
        $authHeader = 'Basic '.base64_encode($admin->email.':password');

        $this->withHeader('Authorization', $authHeader)
            ->postJson(route('api.parser.trigger'), ['workload' => ParserWorkload::Movies->value])
            ->assertAccepted()
            ->assertJson([
                'status' => 'queued',
                'workload' => ParserWorkload::Movies->value,
                'queue' => 'priority-parsing',
            ]);

        Queue::assertPushed(ExecuteParserPipeline::class, function (ExecuteParserPipeline $job): bool {
            return $job->workload === ParserWorkload::Movies && $job->queue === 'priority-parsing';
        });
    }

    public function test_it_returns_localized_validation_errors_for_invalid_workload(): void
    {
        config(['parser.queue' => 'parsing']);
        Queue::fake();

        $admin = User::factory()->admin()->create();
        $authHeader = 'Basic '.base64_encode($admin->email.':password');

        app()->setLocale('es');

        $response = $this->withHeader('Authorization', $authHeader)
            ->postJson(route('api.parser.trigger'), ['workload' => 'invalid']);

        $response
            ->assertUnprocessable()
            ->assertJson([
                'message' => 'La carga de procesamiento seleccionada no es válida.',
            ]);

        $this->assertSame([
            'workload' => ['La carga de procesamiento seleccionada no es válida.'],
        ], $response->json('errors'));

        Queue::assertNothingPushed();
    }

    public function test_admin_users_can_trigger_parser_in_alternate_locale(): void
    {
        config(['parser.queue' => 'french-parsing']);
        Queue::fake();

        $admin = User::factory()->admin()->create();
        $authHeader = 'Basic '.base64_encode($admin->email.':password');

        app()->setLocale('fr');

        $this->withHeader('Authorization', $authHeader)
            ->postJson(route('api.parser.trigger'), ['workload' => ParserWorkload::People->value])
            ->assertAccepted()
            ->assertJson([
                'status' => 'queued',
                'workload' => ParserWorkload::People->value,
                'queue' => 'french-parsing',
            ]);

        Queue::assertPushed(ExecuteParserPipeline::class, function (ExecuteParserPipeline $job): bool {
            return $job->workload === ParserWorkload::People && $job->queue === 'french-parsing';
        });
    }
}
