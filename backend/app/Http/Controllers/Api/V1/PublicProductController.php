<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Product;
use Illuminate\Http\JsonResponse;

class PublicProductController
{
    public function index(): JsonResponse
    {
        $products = Product::with('category')
            ->where('status', 'published')
            ->orderByDesc('featured')
            ->latest()
            ->limit(12)
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Products retrieved successfully.',
            'data' => $products,
        ]);
    }
}
