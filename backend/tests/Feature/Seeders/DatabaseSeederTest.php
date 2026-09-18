<?php

namespace Tests\Feature\Seeders;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_database_seeder_creates_admin_account_and_content_seed_data(): void
    {
        $this->artisan('db:seed')->assertSuccessful();

        $this->assertDatabaseHas('users', [
            'email' => 'info@asili.com',
            'role' => 'admin',
        ]);

        $this->assertTrue(User::query()->where('email', 'info@asili.com')->exists());
    }
}
