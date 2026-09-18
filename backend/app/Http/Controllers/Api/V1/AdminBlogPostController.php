<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\StoreBlogPostRequest;
use App\Http\Requests\UpdateBlogPostRequest;
use App\Models\BlogPost;
use Illuminate\Http\JsonResponse;

class AdminBlogPostController
{
    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Admin blog posts retrieved successfully.',
            'data' => BlogPost::latest()->get(),
        ]);
    }

    public function store(StoreBlogPostRequest $request): JsonResponse
    {
        $post = BlogPost::create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Blog post created successfully.',
            'data' => $post,
        ], 201);
    }

    public function update(UpdateBlogPostRequest $request, BlogPost $blogPost): JsonResponse
    {
        $blogPost->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Blog post updated successfully.',
            'data' => $blogPost->fresh(),
        ]);
    }

    public function destroy(BlogPost $blogPost): JsonResponse
    {
        $blogPost->delete();

        return response()->json([
            'success' => true,
            'message' => 'Blog post deleted successfully.',
            'data' => null,
        ]);
    }
}
