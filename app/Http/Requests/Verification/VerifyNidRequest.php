<?php

namespace App\Http\Requests\Verification;

use Illuminate\Foundation\Http\FormRequest;

class VerifyNidRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function prepareForValidation(): void
    {
        // Accept both formats:
        //   With dashes:    XXX-XXX-XXX-X  (as shown on the physical card)
        //   Without dashes: XXXXXXXXXX     (10 plain digits)
        // Normalise to the dashed format before validation.
        if ($this->filled('nin')) {
            $raw = preg_replace('/[^0-9]/', '', $this->nin);
            if (strlen($raw) === 10) {
                // Format as XXX-XXX-XXX-X
                $this->merge([
                    'nin' => substr($raw, 0, 3) . '-'
                          . substr($raw, 3, 3) . '-'
                          . substr($raw, 6, 3) . '-'
                          . substr($raw, 9, 1),
                ]);
            }
        }
    }

    public function rules(): array
    {
        return [
            // Official DONIDCR format: XXX-XXX-XXX-X  (e.g. 123-456-789-0)
            // Source: nid-card-reprint page regex /^\d{3}-\d{3}-\d{3}-\d{1}$/
            'nin' => [
                'required',
                'string',
                'regex:/^\d{3}-\d{3}-\d{3}-\d{1}$/',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'nin.required' => 'NIN is required.',
            'nin.regex'    => 'NIN must be in the format XXX-XXX-XXX-X (e.g. 123-456-789-0), as printed on your National ID card.',
        ];
    }
}
