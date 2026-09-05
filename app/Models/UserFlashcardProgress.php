<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserFlashcardProgress extends Model
{
    use HasFactory;

    // Statuses
    const STATUS_NEW      = 0;
    const STATUS_LEARNING = 1;
    const STATUS_REVIEWING= 2;
    const STATUS_MASTERED = 3;

    protected $table = 'user_flashcard_progress';

    protected $fillable = [
        'user_id', 'flashcard_id',
        'status', 'times_seen', 'times_correct', 'next_review_at',
    ];

    protected $casts = [
        'status'        => 'integer',
        'times_seen'    => 'integer',
        'times_correct' => 'integer',
        'next_review_at'=> 'datetime',
    ];

    // ── Relationships ──────────────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function flashcard()
    {
        return $this->belongsTo(Flashcard::class);
    }

    // ── Helpers ────────────────────────────────────────────────────────────

    /**
     * Mark a card as reviewed. Updates status using simple spaced repetition.
     * $knew = true means user knew the answer.
     */
    public function review(bool $knew): void
    {
        $this->times_seen++;
        if ($knew) {
            $this->times_correct++;
        }

        // Simple SM-2-like status progression
        $ratio = $this->times_seen > 0 ? $this->times_correct / $this->times_seen : 0;

        if ($this->times_seen >= 5 && $ratio >= 0.9) {
            $this->status = self::STATUS_MASTERED;
            $this->next_review_at = now()->addDays(7);
        } elseif ($this->times_seen >= 2 && $ratio >= 0.7) {
            $this->status = self::STATUS_REVIEWING;
            $this->next_review_at = now()->addDays(3);
        } elseif ($this->times_seen >= 1) {
            $this->status = self::STATUS_LEARNING;
            $this->next_review_at = now()->addHours(12);
        }

        $this->save();
    }

    public static function statusLabel(int $status): string
    {
        return match ($status) {
            self::STATUS_LEARNING  => 'learning',
            self::STATUS_REVIEWING => 'reviewing',
            self::STATUS_MASTERED  => 'mastered',
            default                => 'new',
        };
    }
}
