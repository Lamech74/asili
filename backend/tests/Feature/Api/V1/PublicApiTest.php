<?php

namespace Tests\Feature\Api\V1;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_products_endpoint_returns_successful_payload(): void
    {
        Category::factory()->create(['name' => 'Wellness']);
        Product::factory()->count(2)->create([
            'featured' => true,
            'status' => 'published',
        ]);

        $response = $this->getJson('/api/v1/products');

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.0.name', Product::first()->name)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    ['id', 'name', 'slug', 'status', 'featured'],
                ],
            ]);
    }

    public function test_contact_submission_endpoint_accepts_valid_data(): void
    {
        $payload = [
            'name' => 'Amina Njeri',
            'email' => 'amina@example.com',
            'subject' => 'Product enquiry',
            'message' => 'I would like to know more about your herbal tonics.',
        ];

        $response = $this->postJson('/api/v1/contact', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.name', $payload['name']);

        $this->assertDatabaseHas('contact_messages', ['email' => $payload['email']]);
    }
}
