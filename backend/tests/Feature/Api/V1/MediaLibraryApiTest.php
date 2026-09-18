<?php

namespace Tests\Feature\Api\V1;

use App\Models\MediaAsset;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MediaLibraryApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_media_endpoint_returns_successful_payload(): void
    {
        MediaAsset::factory()->create(['status' => 'published']);

        $response = $this->getJson('/api/v1/media');

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    ['id', 'title', 'file_url', 'status'],
                ],
            ]);
    }

    public function test_admin_can_create_and_delete_media_asset(): void
    {
        $token = User::factory()->create(['role' => 'admin'])
            ->createToken('media-library-test')
            ->plainTextToken;

        $createResponse = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/v1/admin/media', [
                'title' => 'Brand hero',
                'file_url' => 'https://example.com/hero.jpg',
                'mime_type' => 'image/jpeg',
                'category' => 'hero',
                'alt_text' => 'Asili Naturals hero image',
                'featured' => true,
                'status' => 'published',
            ]);

        $createResponse->assertCreated()->assertJsonPath('success', true);

        $asset = MediaAsset::first();

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->deleteJson('/api/v1/admin/media/'.$asset->id)
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertSoftDeleted('media_assets', ['id' => $asset->id]);
    }
}
