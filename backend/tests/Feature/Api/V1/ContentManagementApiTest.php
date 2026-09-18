<?php

namespace Tests\Feature\Api\V1;

use App\Models\BlogPost;
use App\Models\Faq;
use App\Models\SiteSetting;
use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContentManagementApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_content_endpoints_return_successful_payloads(): void
    {
        BlogPost::factory()->create(['status' => 'published']);
        Testimonial::factory()->create(['status' => 'published']);
        Faq::factory()->create(['status' => 'published']);
        SiteSetting::factory()->create(['is_public' => true]);

        $this->getJson('/api/v1/blog')->assertOk()->assertJsonPath('success', true);
        $this->getJson('/api/v1/testimonials')->assertOk()->assertJsonPath('success', true);
        $this->getJson('/api/v1/faqs')->assertOk()->assertJsonPath('success', true);
        $this->getJson('/api/v1/settings')->assertOk()->assertJsonPath('success', true);
    }

    public function test_admin_can_manage_blog_testimonials_faqs_and_settings(): void
    {
        $token = User::factory()->create(['role' => 'admin'])
            ->createToken('content-management-test')
            ->plainTextToken;

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/v1/admin/blog', [
                'title' => 'Morning Rituals',
                'slug' => 'morning-rituals',
                'excerpt' => 'A peaceful start to the day.',
                'content' => 'A gentle morning plan for balance.',
                'featured' => true,
                'status' => 'published',
            ])
            ->assertCreated()
            ->assertJsonPath('success', true);

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/v1/admin/testimonials', [
                'name' => 'Mariam',
                'role' => 'Customer',
                'quote' => 'The products changed my routine for the better.',
                'featured' => true,
                'status' => 'published',
            ])
            ->assertCreated()
            ->assertJsonPath('success', true);

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/v1/admin/faqs', [
                'question' => 'How do I begin?',
                'answer' => 'Start with a small ritual and keep it simple.',
                'featured' => true,
                'status' => 'published',
            ])
            ->assertCreated()
            ->assertJsonPath('success', true);

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/v1/admin/settings', [
                'key' => 'brand_tagline',
                'value' => 'Natural healing, God\'s provision',
                'type' => 'text',
                'is_public' => true,
            ])
            ->assertCreated()
            ->assertJsonPath('success', true);
    }
}
