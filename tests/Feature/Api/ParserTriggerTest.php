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

    #[DataProvider('invalidWorkloadLocaleProvider')]
    public function test_validation_errors_are_translated(string $locale, string $expectedMessage): void
    {
        config(['parser.queue' => 'parsing']);
        Queue::fake();

        $admin = User::factory()->admin()->create();
        $authHeader = 'Basic '.base64_encode($admin->email.':password');

        app()->setLocale($locale);

        $this->withHeader('Authorization', $authHeader)
            ->postJson(route('api.parser.trigger'), ['workload' => 'invalid'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['workload'])
            ->assertJsonPath('errors.workload.0', $expectedMessage);

        Queue::assertNothingPushed();
    }

    /**
     * @return array<int, array{0: string, 1: string}>
     */
    public static function invalidWorkloadLocaleProvider(): array
    {
        return [
            ['en', 'The selected parser workload is invalid.'],
            ['es', 'La carga de trabajo del parser seleccionada no es válida.'],
            ['fr', "La charge de travail du parseur sélectionnée n'est pas valide."],
        ];
    }
}
