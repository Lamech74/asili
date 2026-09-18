<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Testimonial;
use Illuminate\Http\JsonResponse;

class PublicTestimonialController
{
    public function index(): JsonResponse
    {
        $testimonials = Testimonial::query()
            ->where('status', 'published')
            ->orderByDesc('featured')
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Testimonials retrieved successfully.',
            'data' => $testimonials,
        ]);
    }
}
