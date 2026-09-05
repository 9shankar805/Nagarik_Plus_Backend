<?php

namespace App\Services\Verification\Providers;

use App\Services\Verification\Contracts\GovernmentVerificationProvider;
use App\Services\Verification\Contracts\VerificationResult;

/**
 * DONIDCR — Department of National ID and Civil Registration
 *
 * ┌─────────────────────────────────────────────────────────────────────────┐
 * │  INVESTIGATION REPORT — National ID (NIN)                              │
 * │                                                                         │
 * │  Official website : https://donidcr.gov.np                             │
 * │  Citizen portal   : https://citizenportal.donidcr.gov.np               │
 * │  Backend host     : https://api-citizenportal.donidcr.gov.np           │
 * │  Captcha host     : https://captcha-citizenportal.donidcr.gov.np       │
 * │                                                                         │
 * │  Endpoints discovered (Phase 1–3 DevTools analysis):                   │
 * │    GET  /api/captcha          → base64 PNG image (captcha host)        │
 * │    POST /api/v1/mfa/request-otp  → triggers OTP to citizen's phone     │
 * │    POST /api/v1/mfa/verify-otp   → validates OTP, returns downloadToken│
 * │    POST /api/v1/ccn              → lookup by citizenship details        │
 * │    POST /api/v1/enid/download    → download eNID PDF (requires token)  │
 * │    POST /api/audit/log-download  → audit log (internal)                │
 * │                                                                         │
 * │  Required inputs: full_name (Nepali), full_name (English),             │
 * │                   dob_bs (Bikram Sambat), citizenship_issuing_date_bs, │
 * │                   CAPTCHA value, OTP                                    │
 * │                                                                         │
 * │  Authentication    : CAPTCHA (image, no hash) + phone OTP              │
 * │  CAPTCHA required  : YES — image-based, must be solved by human        │
 * │  Session required  : NO persistent session; OTP token is stateless     │
 * │  CORS restriction  : Access-Control-Allow-Origin: citizenportal only   │
 * │  Public API        : NO                                                 │
 * │  Third-party use   : NOT authorized — no MOU / API agreement exists    │
 * │                                                                         │
 * │  Endpoint type     : F — CAPTCHA/session-bound internal endpoint        │
 * │                                                                         │
 * │  ⚠  STATUS: RED                                                        │
 * │     DO NOT use this adapter in production until DONIDCR provides an    │
 * │     official API agreement or MOU for third-party integrations.        │
 * │                                                                         │
 * │  Integration path when authorized:                                      │
 * │    1. Obtain API key / service account from DONIDCR                    │
 * │    2. Replace this stub with a live HTTP client using the key          │
 * │    3. No changes required to controller, routes, or Flutter app        │
 * └─────────────────────────────────────────────────────────────────────────┘
 */
class DonidcrNidProvider implements GovernmentVerificationProvider
{
    public function providerName(): string
    {
        return 'DONIDCR (Department of National ID and Civil Registration)';
    }

    public function verifyNid(array $payload): VerificationResult
    {
        return VerificationResult::pendingAuthorization($this->providerName(), 'National ID (NIN)');
    }

    public function verifyLicence(array $payload): VerificationResult
    {
        return VerificationResult::pendingAuthorization($this->providerName(), 'Driving Licence');
    }

    public function verifyPan(array $payload): VerificationResult
    {
        return VerificationResult::pendingAuthorization($this->providerName(), 'PAN');
    }

    public function verifyCitizenship(array $payload): VerificationResult
    {
        return VerificationResult::pendingAuthorization($this->providerName(), 'Citizenship');
    }

    // ─────────────────────────────────────────────────────────────────────
    // FUTURE IMPLEMENTATION SKETCH (DO NOT ACTIVATE without authorization)
    // ─────────────────────────────────────────────────────────────────────
    //
    // When DONIDCR issues an authorized API key, implement verifyNid() as:
    //
    //   $response = Http::withToken($apiKey)
    //       ->timeout(15)
    //       ->post('https://api-citizenportal.donidcr.gov.np/api/v1/ccn', [
    //           'fullNameLoc'        => $payload['full_name_np'],
    //           'fullName'           => $payload['full_name_en'],
    //           'dobLoc'             => $payload['dob_bs'],
    //           'ccnIssuingDateLoc'  => $payload['citizenship_issuing_date_bs'],
    //       ]);
    //
    //   return new VerificationResult(
    //       verified:  $response->successful(),
    //       status:    $response->successful()
    //                      ? VerificationResult::STATUS_VERIFIED
    //                      : VerificationResult::STATUS_NOT_FOUND,
    //       message:   $response->successful() ? 'NIN verified.' : 'NIN not found.',
    //       reference: $response->json('transactionId'),
    //       meta: [
    //           'nin_loc' => $response->json('data.nin_loc'),
    //       ],
    //   );
    // ─────────────────────────────────────────────────────────────────────
}
