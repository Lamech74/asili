<?php

namespace Tests\Feature\Api\V1;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_products(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Product::factory()->count(2)->create();
        $token = $admin->createToken('admin-test')->plainTextToken;

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/admin/products')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonCount(2, 'data');
    }

    public function test_customer_cannot_view_admin_products(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $token = $customer->createToken('customer-test')->plainTextToken;

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/admin/products')
            ->assertForbidden()
            ->assertJsonPath('success', false);
    }
}
