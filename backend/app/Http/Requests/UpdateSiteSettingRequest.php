<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class UpdateSiteSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'key' => ['sometimes', 'string', 'max:255', Rule::unique('site_settings', 'key')->ignore($this->route('siteSetting'))],
            'value' => ['sometimes', 'nullable'],
            'type' => ['sometimes', 'in:text,number,textarea,boolean,json'],
            'is_public' => ['sometimes', 'boolean'],
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Please correct the site setting details.',
            'errors' => $validator->errors(),
        ], 422));
    }
}
