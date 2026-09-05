<?php

namespace App\Http\Requests\Verification;

use Illuminate\Foundation\Http\FormRequest;

class VerifyCitizenshipRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            // e.g. 01-075-12345  or  01-2075-012345
            'citizenship_number' => ['required', 'string', 'max:30',
                                     'regex:/^[\d\-\/]+$/'],
            'issued_district'    => ['nullable', 'string', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'citizenship_number.required' => 'Citizenship number is required.',
            'citizenship_number.regex'    => 'Citizenship number may only contain digits, hyphens, and slashes.',
        ];
    }
}
