<?php

namespace Database\Factories;

use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Service>
 */
class ServiceFactory extends Factory
{
    protected $model = Service::class;

    public function definition(): array
    {
        $title = $this->faker->unique()->randomElement([
            'Botanical Wellness Consultation',
            'Personal Ritual Planning',
            'Natural Product Guidance',
        ]);

        return [
            'title' => $title,
            'slug' => str($title)->slug()->toString(),
            'summary' => 'Clear, caring guidance for building a natural wellness routine that fits your life.',
            'description' => 'Spend time with an Asili guide to explore your goals, understand our ingredients, and shape a practical ritual with care and clarity.',
            'icon' => $this->faker->randomElement(['leaf', 'sparkles', 'drop', 'heart', 'sun']),
            'featured' => false,
            'status' => 'published',
        ];
    }
}
