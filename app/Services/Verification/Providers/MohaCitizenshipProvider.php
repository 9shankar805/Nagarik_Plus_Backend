<?php

namespace App\Services\Verification\Providers;

use App\Services\Verification\Contracts\GovernmentVerificationProvider;
use App\Services\Verification\Contracts\VerificationResult;

/**
 * MoHA — Ministry of Home Affairs (Nepal)
 *
 * ┌─────────────────────────────────────────────────────────────────────────┐
 * │  INVESTIGATION REPORT — Citizenship Certificate                        │
 * │                                                                         │
 * │  Official website  : https://moha.gov.np                               │
 * │  DAO portals       : https://dao<district>.moha.gov.np                 │
 * │                                                                         │
 * │  Citizenship number format: <district-code>-<year>-<sequence>          │
 * │  (e.g. 01-075-12345)                                                   │
 * │                                                                         │
 * │  Endpoints discovered:                                                  │
 * │    NO online citizenship verification service found                    │
 * │    DAO portals are informational only (PDFs, citizen charters)         │
 * │    No API, no REST endpoint, no web service                            │
 * │                                                                         │
 * │  Authentication    : N/A — no service exists                          │
 * │  CAPTCHA required  : N/A                                               │
 * │  Public API        : NO                                                 │
 * │  Third-party use   : NOT authorized / not possible                     │
 * │                                                                         │
 * │  Endpoint type     : H — No service exists                             │
 * │                                                                         │
 * │  ⚠  STATUS: RED                                                        │
 * │     No online citizenship verification service exists. Contact MoHA   │
 * │     IT Division or DONIDCR (citizenship data may be linked to NID).   │
 * │                                                                         │
 * │  Note: The NID system (DONIDCR) links citizenship data. When DONIDCR  │
 * │  provides authorized API access, citizenship verification may be       │
 * │  achievable through the same pipeline as NID.                         │
 * └─────────────────────────────────────────────────────────────────────────┘
 */
class MohaCitizenshipProvider implements GovernmentVerificationProvider
{
    public function providerName(): string
    {
        return 'MoHA (Ministry of Home Affairs — Citizenship)';
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
}
