<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Service;
use Illuminate\Http\JsonResponse;

class PublicServiceController
{
    public function index(): JsonResponse
    {
        $services = Service::query()
            ->where('status', 'published')
            ->orderByDesc('featured')
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Services retrieved successfully.',
            'data' => $services,
        ]);
    }
}
