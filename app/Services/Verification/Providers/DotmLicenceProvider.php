<?php

namespace App\Services\Verification\Providers;

use App\Services\Verification\Contracts\GovernmentVerificationProvider;
use App\Services\Verification\Contracts\VerificationResult;

/**
 * DoTM — Department of Transport Management
 *
 * ┌─────────────────────────────────────────────────────────────────────────┐
 * │  INVESTIGATION REPORT — Driving Licence                                │
 * │                                                                         │
 * │  Official website  : https://www.dotm.gov.np                           │
 * │  eDL portal        : https://edl.dotm.gov.np  (currently OFFLINE)      │
 * │                                                                         │
 * │  Licence number format : XX-XX-XXXXXXXX  (e.g. 01-01-12345678)        │
 * │  SMS check              : LC <LicenseId> → 33001                       │
 * │                                                                         │
 * │  Endpoints discovered:                                                  │
 * │    edl.dotm.gov.np  → HTTP 000 (unreachable / offline)                 │
 * │    dotm.gov.np/api  → HTTP 404                                         │
 * │                                                                         │
 * │  Third-party tools (e.g. license-checker.acharyanischal.com.np)        │
 * │  maintain their own scraped/cached databases, NOT a live API.          │
 * │  Using those datasets for KYC would be legally and technically         │
 * │  inappropriate.                                                         │
 * │                                                                         │
 * │  Authentication    : Unknown (portal offline)                          │
 * │  CAPTCHA required  : Unknown                                           │
 * │  Public API        : NO                                                 │
 * │  Third-party use   : NOT authorized                                    │
 * │                                                                         │
 * │  Endpoint type     : H — Unknown / portal offline                      │
 * │                                                                         │
 * │  ⚠  STATUS: RED                                                        │
 * │     Contact DoTM (dotm.gov.np) for official API access.                │
 * │                                                                         │
 * │  Integration path when authorized:                                      │
 * │    1. Obtain API key / service account from DoTM                       │
 * │    2. Confirm eDL portal /api/SmartCard/GetSmartCardInfo availability  │
 * │    3. Implement live HTTP client here                                   │
 * └─────────────────────────────────────────────────────────────────────────┘
 */
class DotmLicenceProvider implements GovernmentVerificationProvider
{
    public function providerName(): string
    {
        return 'DoTM (Department of Transport Management)';
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
    // When DoTM restores eDL portal and provides an authorized API key:
    //
    //   $response = Http::withToken($apiKey)
    //       ->timeout(10)
    //       ->get('https://edl.dotm.gov.np/api/SmartCard/GetSmartCardInfo', [
    //           'licenseNumber' => $payload['licence_number'],
    //       ]);
    //
    //   return new VerificationResult(
    //       verified:  $response->successful() && $response->json('status') === 'found',
    //       status:    ...,
    //       message:   ...,
    //       reference: $response->json('referenceId'),
    //   );
    // ─────────────────────────────────────────────────────────────────────
}
