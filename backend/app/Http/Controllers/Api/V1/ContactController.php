<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\ContactMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ContactController
{
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'min:10'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Please correct the submitted contact details.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $message = ContactMessage::create([
            'name' => $request->string('name')->trim(),
            'email' => $request->string('email')->trim(),
            'subject' => $request->string('subject')->trim(),
            'message' => $request->string('message')->trim(),
            'ip_address' => $request->ip(),
            'metadata' => [
                'user_agent' => $request->userAgent(),
            ],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Your message has been received successfully.',
            'data' => $message,
        ], 201);
    }
}
