<?php

namespace Tests\Feature\Api\V1;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RateLimitAndSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_contact_endpoint_allows_submission_and_returns_successful_payload(): void
    {
        $payload = [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'subject' => 'Test enquiry',
            'message' => 'Hello from the rate-limit test.',
        ];

        $response = $this->postJson('/api/v1/contact', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('success', true);
    }
}
