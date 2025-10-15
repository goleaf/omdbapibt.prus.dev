<?php

namespace Tests\Unit\Http\Requests\Subscriptions;

use App\Http\Requests\Subscriptions\StoreSubscriptionRequest;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class StoreSubscriptionRequestTest extends TestCase
{
    public function test_authorizes_authenticated_users(): void
    {
        $user = User::factory()->make();

        $request = StoreSubscriptionRequest::create('/', 'POST');
        $request->setUserResolver(static fn () => $user);

        $this->assertTrue($request->authorize());
    }

    public function test_rejects_guests(): void
    {
        $request = StoreSubscriptionRequest::create('/', 'POST');
        $request->setUserResolver(static fn () => null);

        $this->assertFalse($request->authorize());
    }

    public function test_requires_price_with_translated_message(): void
    {
        $user = User::factory()->make();

        $request = StoreSubscriptionRequest::create('/', 'POST');
        $request->setUserResolver(static fn () => $user);

        $validator = Validator::make($request->all(), $request->rules(), $request->messages());

        $this->assertFalse($validator->passes());
        $this->assertSame(__('subscriptions.errors.price_required'), $validator->errors()->first('price'));
    }
}
