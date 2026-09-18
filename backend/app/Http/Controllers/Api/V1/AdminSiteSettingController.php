<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\StoreSiteSettingRequest;
use App\Http\Requests\UpdateSiteSettingRequest;
use App\Models\SiteSetting;
use Illuminate\Http\JsonResponse;

class AdminSiteSettingController
{
    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Admin site settings retrieved successfully.',
            'data' => SiteSetting::latest()->get(),
        ]);
    }

    public function store(StoreSiteSettingRequest $request): JsonResponse
    {
        $setting = SiteSetting::create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Site setting created successfully.',
            'data' => $setting,
        ], 201);
    }

    public function update(UpdateSiteSettingRequest $request, SiteSetting $siteSetting): JsonResponse
    {
        $siteSetting->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Site setting updated successfully.',
            'data' => $siteSetting->fresh(),
        ]);
    }

    public function destroy(SiteSetting $siteSetting): JsonResponse
    {
        $siteSetting->delete();

        return response()->json([
            'success' => true,
            'message' => 'Site setting deleted successfully.',
            'data' => null,
        ]);
    }
}
