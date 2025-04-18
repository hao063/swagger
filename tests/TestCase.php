<?php

namespace Tests;

use App\Providers\CustomTestResponse;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Testing\TestResponse;

abstract class TestCase extends BaseTestCase
{
    //

    protected function createTestResponse($response, $request): TestResponse
    {
        return CustomTestResponse::fromBaseResponse($response, $request);
    }
}
