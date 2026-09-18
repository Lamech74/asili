<?php

namespace Database\Factories;

use App\Models\BlogPost;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BlogPost>
 */
class BlogPostFactory extends Factory
{
    protected $model = BlogPost::class;

    public function definition(): array
    {
        $title = $this->faker->unique()->randomElement([
            'Starting a Gentle Morning Ritual',
            'Why African Botanicals Belong in Everyday Care',
            'Making Space for Rest and Renewal',
        ]);

        return [
            'title' => $title,
            'slug' => str($title)->slug()->toString(),
            'excerpt' => 'Small, grounded ideas for making wellness feel more natural and sustainable.',
            'content' => 'Wellness does not need to be complicated. Begin with one thoughtful choice, repeat it gently, and allow your routine to grow with you. Nature offers generous starting points when we give ourselves time to notice them.',
            'featured_image' => config('app.url') . '/images/catalogue/journal-placeholder.svg',
            'author_name' => 'The Asili Team',
            'published_at' => now()->subDays(rand(1, 30)),
            'featured' => false,
            'status' => 'published',
        ];
    }
}
