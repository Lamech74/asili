<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateTestimonialRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'role' => ['sometimes', 'nullable', 'string', 'max:255'],
            'quote' => ['sometimes', 'string'],
            'avatar_url' => ['sometimes', 'nullable', 'url', 'max:2048'],
            'featured' => ['sometimes', 'boolean'],
            'status' => ['sometimes', 'in:draft,published,archived'],
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Please correct the testimonial details.',
            'errors' => $validator->errors(),
        ], 422));
    }
}
