<?php

namespace Tests\Unit\Http\Responses\Subscriptions;

use App\Http\Responses\Subscriptions\SubscriptionRedirectResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Session;
use Tests\TestCase;

class SubscriptionRedirectResponseTest extends TestCase
{
    public function test_redirects_to_dashboard_with_flash_status_message(): void
    {
        Session::start();

        $response = SubscriptionRedirectResponse::alreadySubscribed();

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertSame(localized_route('dashboard'), $response->getTargetUrl());
        $this->assertSame(__('subscriptions.status.already_subscribed'), $response->getSession()->get('status'));
    }
}
