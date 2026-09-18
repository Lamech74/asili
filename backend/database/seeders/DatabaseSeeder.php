<?php

namespace Database\Seeders;

use App\Models\BlogPost;
use App\Models\Category;
use App\Models\Faq;
use App\Models\Product;
use App\Models\Service;
use App\Models\SiteSetting;
use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'info@asili.com'],
            [
                'name' => 'Asili Naturals Admin',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
            ]
        );

        $wellness = Category::query()->firstOrCreate([
            'slug' => 'wellness',
        ], [
            'name' => 'Wellness',
            'description' => 'Daily rituals for better balance and calm.',
            'is_featured' => true,
            'status' => 'published',
        ]);

        $ritual = Category::query()->firstOrCreate([
            'slug' => 'rituals',
        ], [
            'name' => 'Rituals',
            'description' => 'Natural care rituals for body and mind.',
            'is_featured' => false,
            'status' => 'published',
        ]);

        Product::factory()->count(4)->create([
            'category_id' => $wellness->id,
            'status' => 'published',
            'featured' => true,
        ]);

        Product::factory()->count(3)->create([
            'category_id' => $ritual->id,
            'status' => 'published',
            'featured' => false,
        ]);

        Service::factory()->count(3)->create([
            'status' => 'published',
            'featured' => true,
        ]);

        BlogPost::factory()->count(3)->create([
            'status' => 'published',
            'featured' => true,
        ]);

        Testimonial::factory()->count(3)->create([
            'status' => 'published',
            'featured' => true,
        ]);

        Faq::factory()->count(4)->create([
            'status' => 'published',
            'featured' => true,
        ]);

        SiteSetting::query()->updateOrCreate(
            ['key' => 'brand_tagline'],
            ['value' => 'Natural healing, God\'s provision', 'type' => 'text', 'is_public' => true]
        );

        SiteSetting::query()->updateOrCreate(
            ['key' => 'contact_email'],
            ['value' => 'hello@asilinaturals.co.ke', 'type' => 'text', 'is_public' => true]
        );
    }
}
