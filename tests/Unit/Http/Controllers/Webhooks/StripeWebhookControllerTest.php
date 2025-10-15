<?php

namespace Tests\Unit\Http\Controllers\Webhooks;

use App\Http\Controllers\Webhooks\StripeWebhookController;
use Tests\TestCase;

class StripeWebhookControllerTest extends TestCase
{
    public function test_it_prefers_metadata_type_when_present(): void
    {
        $payload = [
            'data' => [
                'object' => [
                    'metadata' => [
                        'type' => 'premium',
                        'name' => 'fallback-name',
                    ],
                ],
            ],
        ];

        $result = $this->invokeNewSubscriptionType($payload);

        $this->assertSame('premium', $result);
    }

    public function test_it_falls_back_to_metadata_name_when_type_missing(): void
    {
        $payload = [
            'data' => [
                'object' => [
                    'metadata' => [
                        'name' => 'legacy',
                    ],
                ],
            ],
        ];

        $result = $this->invokeNewSubscriptionType($payload);

        $this->assertSame('legacy', $result);
    }

    public function test_it_defers_to_parent_implementation_without_metadata(): void
    {
        $payload = [
            'data' => [
                'object' => [],
            ],
        ];

        $result = $this->invokeNewSubscriptionType($payload);

        $this->assertSame('default', $result);
    }

    private function invokeNewSubscriptionType(array $payload): string
    {
        $controller = new class extends StripeWebhookController
        {
            public function callNewSubscriptionType(array $payload): string
            {
                return parent::newSubscriptionType($payload);
            }
        };

        return $controller->callNewSubscriptionType($payload);
    }
}
