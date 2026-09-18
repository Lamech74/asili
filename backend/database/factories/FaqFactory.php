<?php

namespace Database\Factories;

use App\Models\Faq;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Faq>
 */
class FaqFactory extends Factory
{
    protected $model = Faq::class;

    public function definition(): array
    {
        return [
            'question' => $this->faker->unique()->randomElement([
                'How should I start an Asili ritual?',
                'Where do you source your botanicals?',
                'How long does delivery take?',
                'Are your products suitable for daily use?',
            ]),
            'answer' => 'Each product includes clear guidance. Start with the recommended amount, listen to your body, and contact us if you need help choosing a suitable ritual.',
            'category' => $this->faker->randomElement(['general', 'shipping', 'wellness', 'products']),
            'featured' => false,
            'status' => 'published',
        ];
    }
}
