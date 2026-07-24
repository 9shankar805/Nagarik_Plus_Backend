<?php

namespace App\Services;

use App\Models\OtpCode;

class OtpService
{
    private const TTL_MINUTES = 10;

    /**
     * Generate a 6-digit OTP, store its sha256 hash, return the plain code.
     * Invalidates any previous unused codes for the same identifier+purpose.
     */
    public function generate(string $identifier, string $purpose): string
    {
        // Invalidate previous codes for same identifier+purpose
        OtpCode::where('identifier', $identifier)
               ->where('purpose', $purpose)
               ->whereNull('used_at')
               ->delete();

        $plain = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        OtpCode::create([
            'identifier' => $identifier,
            'code_hash'  => hash('sha256', $plain),
            'purpose'    => $purpose,
            'expires_at' => now()->addMinutes(self::TTL_MINUTES),
            'used_at'    => null,
        ]);

        return $plain;
    }

    /**
     * Verify an OTP against the stored hash.
     * Returns true only if: record exists, not expired, not used, hash matches.
     * Marks the record as used on success.
     */
    public function verify(string $identifier, string $otp, string $purpose): bool
    {
        $record = OtpCode::where('identifier', $identifier)
                         ->where('purpose', $purpose)
                         ->whereNull('used_at')
                         ->latest()
                         ->first();

        if (!$record) {
            return false;
        }

        if ($record->isExpired()) {
            return false;
        }

        if (hash('sha256', $otp) !== $record->code_hash) {
            return false;
        }

        $record->update(['used_at' => now()]);

        return true;
    }
}
