<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->randomElement([
            'Moringa Daily Powder',
            'Baobab Glow Blend',
            'Hibiscus Calm Tea',
            'Neem and Shea Body Balm',
            'Turmeric Root Tonic',
            'Marula Botanical Oil',
            'Ginger Vitality Blend',
        ]);

        return [
            'category_id' => Category::factory(),
            'name' => $name,
            'slug' => str($name)->slug()->toString(),
            'short_description' => 'A simple botanical essential for a more intentional daily ritual.',
            'description' => 'Made for everyday use with carefully selected African botanicals. Add this gentle ritual to your routine and make space for a slower, more considered approach to wellbeing.',
            'price' => $this->faker->randomFloat(2, 10, 250),
            'compare_at_price' => $this->faker->randomFloat(2, 20, 300),
            'image_url' => config('app.url') . '/images/catalogue/product-placeholder.svg',
            'featured' => false,
            'status' => 'published',
            'meta' => [
                'benefits' => [$this->faker->word(), $this->faker->word()],
            ],
        ];
    }
}
