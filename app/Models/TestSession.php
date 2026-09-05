<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class TestSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'mock_test_id', 'session_token',
        'question_ids', 'adaptive_state', 'started_at', 'expires_at',
        'submitted', 'submitted_at',
    ];

    protected $casts = [
        'question_ids'   => 'array',
        'adaptive_state' => 'array',
        'started_at'   => 'datetime',
        'expires_at'   => 'datetime',
        'submitted'    => 'boolean',
        'submitted_at' => 'datetime',
    ];

    // ── Relationships ──────────────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function mockTest()
    {
        return $this->belongsTo(MockTest::class);
    }

    // ── Helpers ────────────────────────────────────────────────────────────

    public static function generateToken(): string
    {
        return Str::random(64);
    }

    public function isExpired(): bool
    {
        return now()->isAfter($this->expires_at);
    }

    public function isValid(): bool
    {
        return !$this->submitted && !$this->isExpired();
    }

    public function getRemainingSecondsAttribute(): int
    {
        return max(0, now()->diffInSeconds($this->expires_at, false));
    }
}
