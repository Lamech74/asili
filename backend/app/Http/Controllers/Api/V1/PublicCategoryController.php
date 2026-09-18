<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Category;
use Illuminate\Http\JsonResponse;

class PublicCategoryController
{
    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Categories retrieved successfully.',
            'data' => Category::where('status', 'published')
                ->withCount(['products' => fn ($query) => $query->where('status', 'published')])
                ->orderByDesc('is_featured')
                ->orderBy('name')
                ->get(),
        ]);
    }
}
