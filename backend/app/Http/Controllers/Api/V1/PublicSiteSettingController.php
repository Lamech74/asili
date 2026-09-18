<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\SiteSetting;
use Illuminate\Http\JsonResponse;

class PublicSiteSettingController
{
    public function index(): JsonResponse
    {
        $settings = SiteSetting::query()
            ->where('is_public', true)
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Site settings retrieved successfully.',
            'data' => $settings,
        ]);
    }
}
