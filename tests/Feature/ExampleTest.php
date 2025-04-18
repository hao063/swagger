<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/api/test/123?classId=123&keyWork=hao');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'name',
            'email'
        ]);
    }

    public function test_the_application_returns_a_successful_response2(): void
    {
        $response = $this->get('/api/test-2');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'phone',
        ]);
    }

    public function test_the_application_returns_a_successful_response3(): void
    {
        $response = $this->get('/api/test-3');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'phone',
        ]);
    }


}
