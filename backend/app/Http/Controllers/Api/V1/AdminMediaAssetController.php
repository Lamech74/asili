<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\StoreMediaAssetRequest;
use App\Http\Requests\UpdateMediaAssetRequest;
use App\Models\MediaAsset;
use Illuminate\Http\JsonResponse;

class AdminMediaAssetController
{
    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Admin media assets retrieved successfully.',
            'data' => MediaAsset::latest()->get(),
        ]);
    }

    public function store(StoreMediaAssetRequest $request): JsonResponse
    {
        $asset = MediaAsset::create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Media asset created successfully.',
            'data' => $asset,
        ], 201);
    }

    public function update(UpdateMediaAssetRequest $request, MediaAsset $mediaAsset): JsonResponse
    {
        $mediaAsset->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Media asset updated successfully.',
            'data' => $mediaAsset->fresh(),
        ]);
    }

    public function destroy(MediaAsset $mediaAsset): JsonResponse
    {
        $mediaAsset->delete();

        return response()->json([
            'success' => true,
            'message' => 'Media asset deleted successfully.',
            'data' => null,
        ]);
    }
}
