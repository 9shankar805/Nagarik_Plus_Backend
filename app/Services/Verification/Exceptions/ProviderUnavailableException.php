<?php

namespace App\Services\Verification\Exceptions;

use RuntimeException;

/**
 * Thrown when a provider adapter has no authorized integration available.
 * The controller catches this and returns a structured 503 response.
 */
class ProviderUnavailableException extends RuntimeException
{
    public function __construct(
        string $provider,
        string $reason,
        public readonly string $documentType = '',
    ) {
        parent::__construct("[{$provider}] {$reason}");
    }
}
