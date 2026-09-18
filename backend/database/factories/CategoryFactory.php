<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Category>
 */
class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->randomElement(['Herbal Blends', 'Body Care', 'Daily Wellness', 'Natural Rituals', 'Botanical Oils']);

        return [
            'name' => ucfirst($name),
            'slug' => str($name)->slug()->toString(),
            'description' => 'Thoughtful natural essentials selected for simple, restorative daily rituals.',
            'image_url' => config('app.url') . '/images/catalogue/wellness-placeholder.svg',
            'is_featured' => false,
            'status' => 'published',
        ];
    }
}
