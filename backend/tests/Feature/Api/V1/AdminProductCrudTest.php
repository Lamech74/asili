<?php

namespace Tests\Feature\Api\V1;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminProductCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_a_product(): void
    {
        $token = $this->adminToken();
        $category = Category::factory()->create();

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/v1/admin/products', [
                'category_id' => $category->id,
                'name' => 'Moringa Wellness Blend',
                'slug' => 'moringa-wellness-blend',
                'short_description' => 'A grounded daily botanical ritual.',
                'description' => 'A carefully prepared blend for everyday wellness.',
                'price' => 24.50,
                'featured' => true,
                'status' => 'published',
            ]);

        $response->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.slug', 'moringa-wellness-blend');

        $this->assertDatabaseHas('products', [
            'slug' => 'moringa-wellness-blend',
            'status' => 'published',
        ]);
    }

    public function test_product_creation_validates_required_fields(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer '.$this->adminToken())
            ->postJson('/api/v1/admin/products', []);

        $response->assertUnprocessable()
            ->assertJsonPath('success', false)
            ->assertJsonValidationErrors(['name', 'slug', 'price', 'status']);
    }

    public function test_admin_can_update_and_soft_delete_a_product(): void
    {
        $product = Product::factory()->create();
        $token = $this->adminToken();

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->putJson('/api/v1/admin/products/'.$product->id, [
                'name' => 'Updated Botanical Blend',
                'slug' => $product->slug,
                'price' => 39.99,
                'status' => 'draft',
            ])
            ->assertOk()
            ->assertJsonPath('data.name', 'Updated Botanical Blend')
            ->assertJsonPath('data.status', 'draft');

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->deleteJson('/api/v1/admin/products/'.$product->id)
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertSoftDeleted('products', ['id' => $product->id]);
    }

    private function adminToken(): string
    {
        return User::factory()->create(['role' => 'admin'])
            ->createToken('admin-crud-test')
            ->plainTextToken;
    }
}
