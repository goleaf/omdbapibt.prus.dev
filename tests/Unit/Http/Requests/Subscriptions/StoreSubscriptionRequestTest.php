<?php

namespace Tests\Unit\Http\Requests\Subscriptions;

use App\Http\Requests\Subscriptions\StoreSubscriptionRequest;
use Illuminate\Support\Facades\Validator;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class StoreSubscriptionRequestTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        app()->setLocale('en');
    }

    public function test_price_is_required_and_must_be_a_string(): void
    {
        $request = new StoreSubscriptionRequest;

        $validator = Validator::make(
            ['price' => ''],
            $request->rules(),
            $request->messages()
        );

        $this->assertFalse($validator->passes());
        $this->assertSame(
            __('subscriptions.validation.price_required'),
            $validator->errors()->first('price')
        );
    }

    #[DataProvider('localeProvider')]
    public function test_price_required_message_is_translated(string $locale, string $expected): void
    {
        app()->setLocale($locale);

        $request = new StoreSubscriptionRequest;

        $validator = Validator::make(
            ['price' => ''],
            $request->rules(),
            $request->messages()
        );

        $this->assertSame($expected, $validator->errors()->first('price'));

        app()->setLocale('en');
    }

    public static function localeProvider(): array
    {
        return [
            ['en', 'A Stripe price identifier is required to start your trial.'],
            ['es', 'Se requiere un identificador de precio de Stripe para iniciar tu prueba.'],
            ['fr', 'Un identifiant de prix Stripe est requis pour démarrer votre essai.'],
            ['ru', 'Чтобы запустить пробный период, укажите идентификатор цены Stripe.'],
        ];
    }

    public function test_valid_price_passes_validation(): void
    {
        $request = new StoreSubscriptionRequest;

        $validator = Validator::make(
            ['price' => 'price_monthly'],
            $request->rules(),
            $request->messages()
        );

        $this->assertTrue($validator->passes());
    }
}
