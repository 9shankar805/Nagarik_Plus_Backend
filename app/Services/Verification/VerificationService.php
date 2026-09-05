<?php

namespace App\Services\Verification;

use App\Models\VerificationAuditLog;
use App\Services\Verification\Contracts\GovernmentVerificationProvider;
use App\Services\Verification\Contracts\VerificationResult;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Orchestrates all government document verification requests.
 *
 * Responsibilities:
 *  1. Enforce per-user + per-IP rate limits BEFORE calling a provider.
 *  2. Call the correct provider method.
 *  3. Write a sanitised audit log entry (no raw PII).
 *  4. Return the VerificationResult to the controller.
 */
class VerificationService
{
    /**
     * Maximum verification attempts per document type per hour per user/IP.
     * Adjust these conservatively — government endpoints are not free.
     */
    private const RATE_LIMITS = [
        'nid'         => 5,
        'licence'     => 5,
        'pan'         => 5,
        'citizenship' => 5,
    ];

    public function __construct(
        private readonly GovernmentVerificationProvider $provider,
        private readonly ?Application $app = null,
    ) {}

    // ─────────────────────────────────────────────────────────────────────
    // Public API (called by VerificationController)
    // ─────────────────────────────────────────────────────────────────────

    public function verifyNid(array $payload, Request $request): VerificationResult
    {
        return $this->dispatch('nid', $payload, $request,
            fn() => $this->resolveProvider('nid')->verifyNid($payload)
        );
    }

    public function verifyLicence(array $payload, Request $request): VerificationResult
    {
        return $this->dispatch('licence', $payload, $request,
            fn() => $this->resolveProvider('licence')->verifyLicence($payload)
        );
    }

    public function verifyPan(array $payload, Request $request): VerificationResult
    {
        return $this->dispatch('pan', $payload, $request,
            fn() => $this->resolveProvider('pan')->verifyPan($payload)
        );
    }

    public function verifyCitizenship(array $payload, Request $request): VerificationResult
    {
        return $this->dispatch('citizenship', $payload, $request,
            fn() => $this->resolveProvider('citizenship')->verifyCitizenship($payload)
        );
    }

    /**
     * Resolve the provider for a given document type.
     * Falls back to the default injected provider if the named binding
     * is not registered (safe for tests / environments without full DI).
     */
    private function resolveProvider(string $documentType): GovernmentVerificationProvider
    {
        $key = "verification.{$documentType}";

        if ($this->app && $this->app->bound($key)) {
            return $this->app->make($key);
        }

        return $this->provider;
    }

    // ─────────────────────────────────────────────────────────────────────
    // Internals
    // ─────────────────────────────────────────────────────────────────────

    private function dispatch(
        string   $documentType,
        array    $payload,
        Request  $request,
        callable $call,
    ): VerificationResult {

        $userId    = $request->user()?->id;
        $ipAddress = $request->ip() ?? '';

        // 1. Rate-limit check (no provider call if exceeded)
        if ($this->isRateLimited($documentType, $userId, $ipAddress)) {
            $result = new VerificationResult(
                verified: false,
                status:   VerificationResult::STATUS_ERROR,
                message:  'Too many verification attempts. Please wait before trying again.',
            );
            $this->writeAuditLog($documentType, $payload, $result, $userId, $ipAddress, 0);
            return $result;
        }

        // 2. Call the provider and measure latency
        $start = hrtime(true);

        try {
            $result = $call();
        } catch (\Throwable $e) {
            $durationMs = (int) ((hrtime(true) - $start) / 1_000_000);
            Log::warning('GovernmentVerification provider error', [
                'document_type' => $documentType,
                'provider'      => $this->provider->providerName(),
                'error'         => $e->getMessage(),
            ]);
            $result = VerificationResult::unavailable($e->getMessage());
            $this->writeAuditLog($documentType, $payload, $result, $userId, $ipAddress, $durationMs);
            return $result;
        }

        $durationMs = (int) ((hrtime(true) - $start) / 1_000_000);

        // 3. Write sanitised audit log
        $this->writeAuditLog($documentType, $payload, $result, $userId, $ipAddress, $durationMs);

        return $result;
    }

    private function isRateLimited(string $documentType, ?int $userId, string $ipAddress): bool
    {
        $limit  = self::RATE_LIMITS[$documentType] ?? 5;
        $recent = VerificationAuditLog::recentCount($documentType, $userId, $ipAddress, 60);
        return $recent >= $limit;
    }

    private function writeAuditLog(
        string              $documentType,
        array               $payload,
        VerificationResult  $result,
        ?int                $userId,
        string              $ipAddress,
        int                 $durationMs,
    ): void {
        // Determine the raw document number from whichever key is in the payload
        // ONLY to hash it — the plaintext is never stored.
        $rawNumber = match ($documentType) {
            'nid'         => $payload['nin']                ?? '',
            'licence'     => $payload['licence_number']     ?? '',
            'pan'         => $payload['pan']                ?? '',
            'citizenship' => $payload['citizenship_number'] ?? '',
            default       => '',
        };

        try {
            VerificationAuditLog::create([
                'user_id'         => $userId,
                'document_type'   => $documentType,
                'provider'        => $this->provider->providerName(),
                'status'          => $result->status,
                'verified'        => $result->verified,
                'document_hash'   => $rawNumber
                    ? VerificationAuditLog::hashDocument($documentType, $rawNumber)
                    : null,
                'reference_token' => $result->reference,
                'ip_address'      => $ipAddress,
                'user_agent'      => request()->userAgent(),
                'duration_ms'     => $durationMs,
            ]);
        } catch (\Throwable $e) {
            // Never let audit log failure bubble up to the user
            Log::error('VerificationAuditLog write failed', ['error' => $e->getMessage()]);
        }
    }
}
