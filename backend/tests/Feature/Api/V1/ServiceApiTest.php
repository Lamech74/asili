<?php

namespace Tests\Feature\Api\V1;

use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_services_endpoint_returns_successful_payload(): void
    {
        Service::factory()->count(2)->create([
            'status' => 'published',
        ]);

        $response = $this->getJson('/api/v1/services');

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    ['id', 'title', 'slug', 'summary', 'status'],
                ],
            ]);
    }

    public function test_admin_can_create_update_and_delete_a_service(): void
    {
        $token = User::factory()->create(['role' => 'admin'])
            ->createToken('service-api-test')
            ->plainTextToken;

        $createResponse = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/v1/admin/services', [
                'title' => 'Detox Ritual',
                'slug' => 'detox-ritual',
                'summary' => 'An intentional reset for the body and mind.',
                'description' => 'Gentle cleansing support rooted in natural ingredients.',
                'icon' => 'leaf',
                'featured' => true,
                'status' => 'published',
            ]);

        $createResponse->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.slug', 'detox-ritual');

        $service = Service::first();

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->putJson('/api/v1/admin/services/'.$service->id, [
                'title' => 'Updated Detox Ritual',
                'slug' => 'updated-detox-ritual',
                'summary' => 'An updated reset for the body and mind.',
                'description' => 'Updated description.',
                'status' => 'draft',
            ])
            ->assertOk()
            ->assertJsonPath('data.title', 'Updated Detox Ritual');

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->deleteJson('/api/v1/admin/services/'.$service->id)
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertSoftDeleted('services', ['id' => $service->id]);
    }
}
