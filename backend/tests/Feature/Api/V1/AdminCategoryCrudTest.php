<?php

namespace Tests\Feature\Api\V1;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCategoryCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_update_and_delete_a_category(): void
    {
        $token = $this->adminToken();

        $created = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/v1/admin/categories', [
                'name' => 'Botanical Blends',
                'slug' => 'botanical-blends',
                'description' => 'Ingredient-led wellness essentials.',
                'status' => 'published',
                'is_featured' => true,
            ])
            ->assertCreated()
            ->assertJsonPath('data.slug', 'botanical-blends');

        $categoryId = $created->json('data.id');

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->putJson('/api/v1/admin/categories/'.$categoryId, [
                'name' => 'Daily Botanical Blends',
                'slug' => 'daily-botanical-blends',
            ])
            ->assertOk()
            ->assertJsonPath('data.name', 'Daily Botanical Blends');

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->deleteJson('/api/v1/admin/categories/'.$categoryId)
            ->assertOk();

        $this->assertDatabaseMissing('categories', ['id' => $categoryId]);
    }

    public function test_category_creation_requires_a_unique_name_and_slug(): void
    {
        Category::factory()->create(['name' => 'Existing', 'slug' => 'existing']);

        $this->withHeader('Authorization', 'Bearer '.$this->adminToken())
            ->postJson('/api/v1/admin/categories', [
                'name' => 'Existing',
                'slug' => 'existing',
                'status' => 'published',
            ])
            ->assertUnprocessable()
            ->assertJsonPath('success', false)
            ->assertJsonValidationErrors(['name', 'slug']);
    }

    public function test_category_with_products_cannot_be_deleted(): void
    {
        $category = Category::factory()->create();
        Product::factory()->create(['category_id' => $category->id]);

        $this->withHeader('Authorization', 'Bearer '.$this->adminToken())
            ->deleteJson('/api/v1/admin/categories/'.$category->id)
            ->assertStatus(409)
            ->assertJsonPath('success', false);
    }

    private function adminToken(): string
    {
        return User::factory()->create(['role' => 'admin'])
            ->createToken('category-crud-test')
            ->plainTextToken;
    }
}
