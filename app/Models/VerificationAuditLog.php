<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Audit log for every government document verification attempt.
 *
 * Security rules enforced here:
 *  - Raw document numbers (NIN, PAN, licence, citizenship) are NEVER stored.
 *  - Only a sha256 hash of the number is stored for deduplication/abuse checks.
 *  - IP address and user-agent are stored for rate-limit and fraud detection only.
 *  - Full PII (name, DOB, address) is never written to this table.
 */
class VerificationAuditLog extends Model
{
    protected $fillable = [
        'user_id',
        'document_type',
        'provider',
        'status',
        'verified',
        'document_hash',
        'reference_token',
        'ip_address',
        'user_agent',
        'duration_ms',
    ];

    protected $casts = [
        'verified'    => 'boolean',
        'duration_ms' => 'integer',
    ];

    // ── Relationships ──────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ── Helpers ────────────────────────────────────────────────────────────

    /**
     * Hash a sensitive document number for deduplication.
     * We prepend the document_type as a namespace so the same number
     * does not produce the same hash across different document types.
     * NIN is normalised to digits-only before hashing so that
     * "123-456-789-0" and "1234567890" produce the same hash.
     */
    public static function hashDocument(string $documentType, string $number): string
    {
        // Strip non-numeric characters for consistent hashing across formats
        $normalised = $documentType === 'nid'
            ? preg_replace('/[^0-9]/', '', $number)
            : trim($number);

        return hash('sha256', $documentType . ':' . $normalised);
    }

    /**
     * Count recent attempts for abuse detection.
     */
    public static function recentCount(
        string $documentType,
        ?int   $userId = null,
        string $ipAddress = '',
        int    $windowMinutes = 60
    ): int {
        return static::where('document_type', $documentType)
            ->where('created_at', '>=', now()->subMinutes($windowMinutes))
            ->when($userId,     fn($q) => $q->where('user_id', $userId))
            ->when($ipAddress,  fn($q) => $q->orWhere('ip_address', $ipAddress))
            ->count();
    }
}
