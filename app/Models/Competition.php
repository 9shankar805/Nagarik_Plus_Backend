<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Competition extends Model
{
    use HasFactory;

    // Status constants
    const STATUS_DRAFT     = 'draft';
    const STATUS_OPEN      = 'open';
    const STATUS_ONGOING   = 'ongoing';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'learning_category_id', 'mock_test_id',
        'title', 'title_np', 'description', 'description_np', 'banner_url',
        'registration_open_at', 'registration_close_at', 'starts_at', 'ends_at',
        'max_participants', 'entry_fee', 'is_free',
        'prize_pool', 'prize_distribution',
        'status', 'winners_announced_at', 'created_by',
    ];

    protected $casts = [
        'prize_distribution'       => 'array',
        'registration_open_at'     => 'datetime',
        'registration_close_at'    => 'datetime',
        'starts_at'                => 'datetime',
        'ends_at'                  => 'datetime',
        'winners_announced_at'     => 'datetime',
        'is_free'                  => 'boolean',
        'entry_fee'                => 'decimal:2',
        'prize_pool'               => 'decimal:2',
        'max_participants'         => 'integer',
    ];

    // ── Relationships ──────────────────────────────────────────────────────

    public function category()
    {
        return $this->belongsTo(LearningCategory::class, 'learning_category_id');
    }

    public function mockTest()
    {
        return $this->belongsTo(MockTest::class);
    }

    public function registrations()
    {
        return $this->hasMany(CompetitionRegistration::class);
    }

    public function attempts()
    {
        return $this->hasMany(CompetitionAttempt::class);
    }

    public function leaderboard()
    {
        return $this->hasMany(LeaderboardEntry::class)->orderBy('rank');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ── Scopes ─────────────────────────────────────────────────────────────

    public function scopeOpen($query)
    {
        return $query->where('status', self::STATUS_OPEN);
    }

    public function scopeOngoing($query)
    {
        return $query->where('status', self::STATUS_ONGOING);
    }

    public function scopeVisible($query)
    {
        return $query->whereIn('status', [self::STATUS_OPEN, self::STATUS_ONGOING, self::STATUS_COMPLETED]);
    }

    // ── Helpers ────────────────────────────────────────────────────────────

    public function isRegistrationOpen(): bool
    {
        $now = now();
        if ($this->status !== self::STATUS_OPEN) {
            return false;
        }
        if ($this->registration_open_at && $now->isBefore($this->registration_open_at)) {
            return false;
        }
        if ($this->registration_close_at && $now->isAfter($this->registration_close_at)) {
            return false;
        }
        if ($this->max_participants) {
            return $this->registrations()->where('payment_status', 'paid')->count() < $this->max_participants;
        }
        return true;
    }

    public function isUserRegistered(int $userId): bool
    {
        return $this->registrations()->where('user_id', $userId)->exists();
    }

    public function hasUserAttempted(int $userId): bool
    {
        return $this->attempts()->where('user_id', $userId)->exists();
    }

    public function getPrizeForRank(int $rank): float
    {
        if (!$this->prize_distribution) {
            return 0;
        }
        foreach ($this->prize_distribution as $prize) {
            if (($prize['rank'] ?? null) === $rank) {
                return (float) ($prize['amount'] ?? 0);
            }
        }
        return 0;
    }

    public function getParticipantCountAttribute(): int
    {
        return $this->registrations()->where('payment_status', 'paid')->count();
    }
}
