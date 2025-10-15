<?php

namespace Tests\Unit\Models;

use App\Models\UiTranslation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UiTranslationTest extends TestCase
{
    use RefreshDatabase;

    public function test_value_field_is_translatable(): void
    {
        $translation = UiTranslation::factory()->create([
            'value' => [
                'en' => 'Sign in',
                'es' => 'Iniciar sesión',
            ],
        ]);

        $this->assertSame('Sign in', $translation->getTranslation('value', 'en'));
        $this->assertSame('Iniciar sesión', $translation->getTranslation('value', 'es'));
    }

    public function test_scope_orders_by_group_then_key(): void
    {
        $second = UiTranslation::factory()->create([
            'group' => 'dashboard',
            'key' => 'stats',
        ]);
        $first = UiTranslation::factory()->create([
            'group' => 'auth',
            'key' => 'login',
        ]);

        $ordered = UiTranslation::ordered()->pluck('id')->all();

        $this->assertSame([$first->id, $second->id], $ordered);
    }
}
