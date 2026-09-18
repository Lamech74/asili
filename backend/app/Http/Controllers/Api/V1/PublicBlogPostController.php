<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\BlogPost;
use Illuminate\Http\JsonResponse;

class PublicBlogPostController
{
    public function index(): JsonResponse
    {
        $posts = BlogPost::query()
            ->where('status', 'published')
            ->orderByDesc('featured')
            ->latest('published_at')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Blog posts retrieved successfully.',
            'data' => $posts,
        ]);
    }
}
