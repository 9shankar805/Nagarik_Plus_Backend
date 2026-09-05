<?php

namespace App\Services\Verification\Contracts;

use JsonSerializable;

/**
 * Immutable DTO returned by every verification provider.
 *
 * $verified   — true only when the government service positively confirmed
 *               the document exists and the supplied details match.
 * $status     — one of: verified | not_found | mismatch | pending_authorization
 *                        | captcha_required | provider_unavailable | error
 * $message    — human-readable explanation (never contains raw PII).
 * $reference  — opaque reference token from the government service (nullable).
 * $meta       — any additional non-sensitive key/value data the provider returns.
 */
final class VerificationResult implements JsonSerializable
{
    public const STATUS_VERIFIED              = 'verified';
    public const STATUS_NOT_FOUND             = 'not_found';
    public const STATUS_MISMATCH              = 'mismatch';
    public const STATUS_PENDING_AUTHORIZATION = 'pending_authorization';
    public const STATUS_CAPTCHA_REQUIRED      = 'captcha_required';
    public const STATUS_PROVIDER_UNAVAILABLE  = 'provider_unavailable';
    public const STATUS_ERROR                 = 'error';

    public function __construct(
        public readonly bool    $verified,
        public readonly string  $status,
        public readonly string  $message,
        public readonly ?string $reference = null,
        public readonly array   $meta      = [],
    ) {}

    public static function unavailable(string $reason): self
    {
        return new self(
            verified:  false,
            status:    self::STATUS_PROVIDER_UNAVAILABLE,
            message:   $reason,
        );
    }

    public static function pendingAuthorization(string $providerName, string $documentType): self
    {
        return new self(
            verified: false,
            status:   self::STATUS_PENDING_AUTHORIZATION,
            message:  "The {$documentType} verification provider ({$providerName}) requires an "
                    . 'official government API agreement before it can be used. '
                    . 'Please contact the relevant government authority for authorized API access.',
            meta: [
                'provider'       => $providerName,
                'document_type'  => $documentType,
                'action_required'=> 'Contact government authority for official API/MOU.',
            ],
        );
    }

    public function toArray(): array
    {
        return [
            'verified'  => $this->verified,
            'status'    => $this->status,
            'message'   => $this->message,
            'reference' => $this->reference,
            'meta'      => $this->meta,
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
