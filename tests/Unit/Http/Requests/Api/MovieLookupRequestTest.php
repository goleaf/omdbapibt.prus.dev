<?php

namespace Tests\Unit\Http\Requests\Api;

use App\Http\Requests\Api\MovieLookupRequest;
use PHPUnit\Framework\TestCase;

class MovieLookupRequestTest extends TestCase
{
    public function test_it_allows_any_user(): void
    {
        $request = new MovieLookupRequest;

        $this->assertTrue($request->authorize());
    }

    public function test_it_defines_expected_rules(): void
    {
        $request = new MovieLookupRequest;

        $this->assertSame([
            'query' => ['required', 'string', 'min:2'],
            'limit' => ['sometimes', 'integer', 'min:1', 'max:50'],
        ], $request->rules());
    }
}
