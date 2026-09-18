<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\MediaAsset;
use Illuminate\Http\JsonResponse;

class PublicMediaAssetController
{
    public function index(): JsonResponse
    {
        $assets = MediaAsset::query()
            ->where('status', 'published')
            ->orderByDesc('featured')
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Media assets retrieved successfully.',
            'data' => $assets,
        ]);
    }
}
