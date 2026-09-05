<?php

namespace App\Services\Verification;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * CaptchaProxyService — Phase 6 CAPTCHA-aware flow
 *
 * The Nepal government DONIDCR portal requires a human-solved image CAPTCHA
 * before any verification can proceed. This service acts as a controlled
 * proxy that:
 *
 *   1. Fetches the CAPTCHA image from the official government server
 *      (captcha-citizenportal.donidcr.gov.np).
 *   2. Returns the base64 image to the Flutter app.
 *   3. The user sees and types the CAPTCHA in the app.
 *   4. The app submits the CAPTCHA value along with the document details.
 *
 * Security guardrails enforced here:
 *  - We only proxy the image — we never auto-solve it.
 *  - No CAPTCHA bypass of any kind is attempted.
 *  - This flow is ONLY activated when DONIDCR has granted API access.
 *    Until then, getCaptchaImage() returns null and the flow is disabled.
 *
 * Flow diagram:
 *
 *   Flutter App
 *       │  GET /api/v1/verification/captcha/nid
 *       ▼
 *   CaptchaProxyService::getCaptchaImage()
 *       │  GET captcha-citizenportal.donidcr.gov.np/api/captcha
 *       ▼
 *   { image: "base64..." }  →  returned to Flutter
 *
 *   User solves CAPTCHA visually → types value in app
 *
 *   Flutter App
 *       │  POST /api/v1/verification/nid  { nin, captcha }
 *       ▼
 *   VerificationController → DonidcrNidProvider (when authorized)
 *       │  POST api-citizenportal.donidcr.gov.np/api/v1/mfa/request-otp
 *       ▼
 *   Government sends OTP to citizen's registered phone
 *
 *   Flutter App
 *       │  POST /api/v1/verification/nid/confirm  { transaction_id, otp }
 *       ▼
 *   DonidcrNidProvider  →  POST /api/v1/mfa/verify-otp
 *       ▼
 *   Verification result
 */
class CaptchaProxyService
{
    private const CAPTCHA_URL = 'https://captcha-citizenportal.donidcr.gov.np/api/captcha';

    /**
     * Fetch a fresh CAPTCHA image from DONIDCR and return the base64 string.
     *
     * Returns null if the endpoint is unreachable or not yet authorized.
     * The caller must check for null and return a 503 to the Flutter app.
     *
     * NOTE: This method MUST NOT be called in production until DONIDCR has
     * granted explicit API access to this application.
     */
    public function getCaptchaImage(): ?string
    {
        // Guard: only allowed when explicitly enabled via config
        if (! config('verification.captcha_proxy_enabled', false)) {
            return null;
        }

        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'Accept' => 'application/json',
                ])
                ->get(self::CAPTCHA_URL);

            if ($response->successful() && $response->json('image')) {
                return $response->json('image');
            }

            Log::warning('CaptchaProxy: unexpected response from DONIDCR', [
                'status' => $response->status(),
            ]);

            return null;
        } catch (\Throwable $e) {
            Log::warning('CaptchaProxy: failed to fetch CAPTCHA', [
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }
}
