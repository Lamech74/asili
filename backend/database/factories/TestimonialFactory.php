<?php

namespace Database\Factories;

use App\Models\Testimonial;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Testimonial>
 */
class TestimonialFactory extends Factory
{
    protected $model = Testimonial::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->randomElement(['Amina Wanjiku', 'Brian Otieno', 'Lydia Njeri']),
            'role' => $this->faker->randomElement(['Customer', 'Wellness Customer', 'Parent', 'Wellbeing Consultant']),
            'quote' => 'The ritual feels simple enough to keep and thoughtful enough to look forward to. I appreciate the care behind every ingredient.',
            'avatar_url' => null,
            'featured' => false,
            'status' => 'published',
        ];
    }
}
