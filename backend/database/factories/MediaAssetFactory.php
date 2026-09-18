<?php

namespace Database\Factories;

use App\Models\MediaAsset;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MediaAsset>
 */
class MediaAssetFactory extends Factory
{
    protected $model = MediaAsset::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(4),
            'file_url' => $this->faker->imageUrl(1200, 800, 'wellness', true),
            'mime_type' => 'image/jpeg',
            'category' => $this->faker->randomElement(['product', 'blog', 'hero', 'brand']),
            'alt_text' => $this->faker->sentence(5),
            'featured' => false,
            'status' => 'published',
        ];
    }
}
