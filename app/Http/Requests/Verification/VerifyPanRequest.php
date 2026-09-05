<?php

namespace App\Http\Requests\Verification;

use Illuminate\Foundation\Http\FormRequest;

class VerifyPanRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            // Nepal PAN is a 9-digit numeric code
            'pan' => ['required', 'string', 'regex:/^\d{9}$/',
                      'not_regex:/^(.)\1+$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'pan.required' => 'PAN is required.',
            'pan.regex'    => 'PAN must be exactly 9 digits.',
        ];
    }
}
