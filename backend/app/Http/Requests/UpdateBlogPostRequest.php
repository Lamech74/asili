<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class UpdateBlogPostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'string', 'max:255'],
            'slug' => ['sometimes', 'string', 'max:255', Rule::unique('blog_posts', 'slug')->ignore($this->route('blogPost'))],
            'excerpt' => ['sometimes', 'nullable', 'string', 'max:500'],
            'content' => ['sometimes', 'nullable', 'string'],
            'featured_image' => ['sometimes', 'nullable', 'url', 'max:2048'],
            'author_name' => ['sometimes', 'nullable', 'string', 'max:255'],
            'published_at' => ['sometimes', 'nullable', 'date'],
            'featured' => ['sometimes', 'boolean'],
            'status' => ['sometimes', 'in:draft,published,archived'],
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Please correct the blog post details.',
            'errors' => $validator->errors(),
        ], 422));
    }
}
