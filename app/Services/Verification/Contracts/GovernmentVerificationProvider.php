<?php

namespace App\Services\Verification\Contracts;

/**
 * Contract that every government verification adapter must implement.
 *
 * Each method returns a VerificationResult DTO.
 * If the provider is not yet available (all current Nepal services are RED),
 * it throws \App\Services\Verification\Exceptions\ProviderUnavailableException.
 *
 * When an official API / MOU is established, replace the stub adapter
 * with a live adapter — the controller and routes never change.
 */
interface GovernmentVerificationProvider
{
    /**
     * Verify a National Identity Number (NIN).
     *
     * @param  array{nin: string, full_name?: string, dob_bs?: string}  $payload
     */
    public function verifyNid(array $payload): VerificationResult;

    /**
     * Verify a Driving Licence number.
     *
     * @param  array{licence_number: string}  $payload
     */
    public function verifyLicence(array $payload): VerificationResult;

    /**
     * Verify a PAN (Permanent Account Number) with the IRD.
     *
     * @param  array{pan: string}  $payload
     */
    public function verifyPan(array $payload): VerificationResult;

    /**
     * Verify a Citizenship certificate number.
     *
     * @param  array{citizenship_number: string, issued_district?: string}  $payload
     */
    public function verifyCitizenship(array $payload): VerificationResult;

    /**
     * Human-readable name of this adapter (used in audit logs).
     */
    public function providerName(): string;
}
