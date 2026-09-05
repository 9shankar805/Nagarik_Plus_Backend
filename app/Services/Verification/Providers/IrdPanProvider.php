<?php

namespace App\Services\Verification\Providers;

use App\Services\Verification\Contracts\GovernmentVerificationProvider;
use App\Services\Verification\Contracts\VerificationResult;

/**
 * IRD — Inland Revenue Department (Nepal)
 *
 * ┌─────────────────────────────────────────────────────────────────────────┐
 * │  INVESTIGATION REPORT — PAN (Permanent Account Number)                 │
 * │                                                                         │
 * │  Official website     : https://ird.gov.np                             │
 * │  Taxpayer portal      : https://taxpayerportal.ird.gov.np (403)        │
 * │  Legacy web service   : http://webservice.ird.gov.np (404)             │
 * │  Tax clearance search : https://ird.gov.np/tax-clearance-search        │
 * │                                                                         │
 * │  PAN format           : 9-digit numeric (e.g. 123456789)               │
 * │                                                                         │
 * │  Endpoints discovered:                                                  │
 * │    ird.gov.np taxpayer search → server-rendered HTML form (POST)       │
 * │    webservice.ird.gov.np/WebService.asmx → HTTP 404 (decommissioned)   │
 * │    taxpayerportal.ird.gov.np → HTTP 403 (IP/auth restricted)           │
 * │    No JSON REST API found                                               │
 * │                                                                         │
 * │  Authentication    : Session/CSRF (form-based)                        │
 * │  CAPTCHA required  : Likely (form-based pages often have CAPTCHA)     │
 * │  Public API        : NO                                                 │
 * │  Third-party use   : NOT authorized                                    │
 * │                                                                         │
 * │  Endpoint type     : C — Internal website endpoint (form-based)        │
 * │                                                                         │
 * │  ⚠  STATUS: RED                                                        │
 * │     Contact IRD (ird.gov.np) for official API or DST integration.      │
 * │                                                                         │
 * │  Integration path when authorized:                                      │
 * │    IRD may provide a SOAP/REST API for authorized financial            │
 * │    institutions. Contact IRD IT Division for API MOU.                 │
 * └─────────────────────────────────────────────────────────────────────────┘
 */
class IrdPanProvider implements GovernmentVerificationProvider
{
    public function providerName(): string
    {
        return 'IRD (Inland Revenue Department)';
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
    // When IRD provides authorized API access:
    //
    //   $response = Http::withBasicAuth($username, $password)
    //       ->timeout(15)
    //       ->post('https://api.ird.gov.np/v1/taxpayer/verify', [
    //           'pan' => $payload['pan'],
    //       ]);
    //
    //   return new VerificationResult(
    //       verified:  $response->json('found') === true,
    //       status:    $response->json('found')
    //                      ? VerificationResult::STATUS_VERIFIED
    //                      : VerificationResult::STATUS_NOT_FOUND,
    //       message:   $response->json('found') ? 'PAN verified.' : 'PAN not found.',
    //       reference: $response->json('referenceId'),
    //       meta: [
    //           // Return only non-sensitive confirmation fields, never full name
    //           'taxpayer_type' => $response->json('taxpayerType'),
    //           'registered_at' => $response->json('registrationDate'),
    //       ],
    //   );
    // ─────────────────────────────────────────────────────────────────────
}
