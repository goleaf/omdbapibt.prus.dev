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

    public function test_validation_errors_are_localized_for_invalid_workloads(): void
    {
        config(['parser.queue' => 'parsing']);
        Queue::fake();

        $this->app->setLocale('es');

        $admin = User::factory()->admin()->create();
        $authHeader = 'Basic '.base64_encode($admin->email.':password');

        $this->withHeader('Authorization', $authHeader)
            ->postJson(route('api.parser.trigger'), ['workload' => 'invalid-workload'])
            ->assertUnprocessable()
            ->assertJsonPath('message', __('validation.The given data was invalid.'))
            ->assertJsonPath('errors.workload.0', __('validation.custom.workload.enum'));

        Queue::assertNothingPushed();

        $this->app->setLocale('en');
    }

    public function test_admin_users_can_trigger_parser_in_french_locale(): void
    {
        config(['parser.queue' => 'priority-parsing']);
        Queue::fake();

        $this->app->setLocale('fr');

        $admin = User::factory()->admin()->create();
        $authHeader = 'Basic '.base64_encode($admin->email.':password');

        $response = $this->withHeader('Authorization', $authHeader)
            ->postJson(route('api.parser.trigger'), ['workload' => ParserWorkload::People->value]);

        $response->assertAccepted()
            ->assertJson([
                'status' => 'queued',
                'workload' => ParserWorkload::People->value,
                'queue' => 'priority-parsing',
            ]);

        Queue::assertPushed(ExecuteParserPipeline::class, function (ExecuteParserPipeline $job): bool {
            return $job->workload === ParserWorkload::People && $job->queue === 'priority-parsing';
        });

        $this->app->setLocale('en');
    }
}
