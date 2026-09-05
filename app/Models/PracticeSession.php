<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PracticeSession extends Model
{
    use HasFactory;

    // Modes
    const MODE_ALL        = 'all';
    const MODE_WRONG_ONLY = 'wrong_only';
    const MODE_BOOKMARKED = 'bookmarked';
    const MODE_DIFFICULTY = 'difficulty';

    protected $fillable = [
        'user_id', 'category', 'learning_chapter_id', 'mode', 'difficulty',
        'questions_answered', 'correct_answers', 'answers',
        'time_taken_seconds', 'completed_at',
    ];

    protected $casts = [
        'answers'            => 'array',
        'completed_at'       => 'datetime',
        'questions_answered' => 'integer',
        'correct_answers'    => 'integer',
        'time_taken_seconds' => 'integer',
    ];

    // ── Relationships ──────────────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function chapter()
    {
        return $this->belongsTo(LearningChapter::class, 'learning_chapter_id');
    }

    // ── Helpers ────────────────────────────────────────────────────────────

    public function getAccuracyAttribute(): int
    {
        return $this->questions_answered > 0
            ? round(($this->correct_answers / $this->questions_answered) * 100)
            : 0;
    }
}
