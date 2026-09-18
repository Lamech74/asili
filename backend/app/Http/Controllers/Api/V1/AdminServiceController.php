<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\StoreServiceRequest;
use App\Http\Requests\UpdateServiceRequest;
use App\Models\Service;
use Illuminate\Http\JsonResponse;

class AdminServiceController
{
    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Admin services retrieved successfully.',
            'data' => Service::latest()->get(),
        ]);
    }

    public function store(StoreServiceRequest $request): JsonResponse
    {
        $service = Service::create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Service created successfully.',
            'data' => $service,
        ], 201);
    }

    public function update(UpdateServiceRequest $request, Service $service): JsonResponse
    {
        $service->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Service updated successfully.',
            'data' => $service->fresh(),
        ]);
    }

    public function destroy(Service $service): JsonResponse
    {
        $service->delete();

        return response()->json([
            'success' => true,
            'message' => 'Service deleted successfully.',
            'data' => null,
        ]);
    }
}
