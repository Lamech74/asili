<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Faq;
use Illuminate\Http\JsonResponse;

class PublicFaqController
{
    public function index(): JsonResponse
    {
        $faqs = Faq::query()
            ->where('status', 'published')
            ->orderByDesc('featured')
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'FAQs retrieved successfully.',
            'data' => $faqs,
        ]);
    }
}
