<?php

namespace App\Http\Requests\Verification;

use Illuminate\Foundation\Http\FormRequest;

class VerifyLicenceRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            // Nepal licence format: XX-XX-XXXXXXXX (e.g. 01-01-12345678)
            'licence_number' => ['required', 'string',
                                 'regex:/^\d{2}-\d{2}-\d{8}$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'licence_number.required' => 'Licence number is required.',
            'licence_number.regex'    => 'Licence number must be in format XX-XX-XXXXXXXX (e.g. 01-01-12345678).',
        ];
    }
}
