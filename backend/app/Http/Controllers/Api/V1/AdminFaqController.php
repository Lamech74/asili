<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\StoreFaqRequest;
use App\Http\Requests\UpdateFaqRequest;
use App\Models\Faq;
use Illuminate\Http\JsonResponse;

class AdminFaqController
{
    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Admin FAQs retrieved successfully.',
            'data' => Faq::latest()->get(),
        ]);
    }

    public function store(StoreFaqRequest $request): JsonResponse
    {
        $faq = Faq::create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'FAQ created successfully.',
            'data' => $faq,
        ], 201);
    }

    public function update(UpdateFaqRequest $request, Faq $faq): JsonResponse
    {
        $faq->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'FAQ updated successfully.',
            'data' => $faq->fresh(),
        ]);
    }

    public function destroy(Faq $faq): JsonResponse
    {
        $faq->delete();

        return response()->json([
            'success' => true,
            'message' => 'FAQ deleted successfully.',
            'data' => null,
        ]);
    }
}
