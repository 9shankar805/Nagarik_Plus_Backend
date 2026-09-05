<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyQuiz extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_question_id', 'learning_category_id', 'quiz_date', 'is_active',
    ];

    protected $casts = [
        'quiz_date' => 'date',
        'is_active' => 'boolean',
    ];

    // ── Relationships ──────────────────────────────────────────────────────

    public function question()
    {
        return $this->belongsTo(QuizQuestion::class, 'quiz_question_id');
    }

    public function category()
    {
        return $this->belongsTo(LearningCategory::class, 'learning_category_id');
    }

    public function entries()
    {
        return $this->hasMany(DailyQuizEntry::class);
    }

    // ── Scopes ─────────────────────────────────────────────────────────────

    public function scopeForToday($query)
    {
        return $query->where('quiz_date', today())->where('is_active', true);
    }

    public function scopeForDate($query, string $date)
    {
        return $query->where('quiz_date', $date)->where('is_active', true);
    }

    // ── Helpers ────────────────────────────────────────────────────────────

    public function hasUserAnswered(int $userId): bool
    {
        return $this->entries()->where('user_id', $userId)->exists();
    }

    public function getUserEntry(int $userId): ?DailyQuizEntry
    {
        return $this->entries()->where('user_id', $userId)->first();
    }

    public function getParticipantCountAttribute(): int
    {
        return $this->entries()->count();
    }

    public function getCorrectCountAttribute(): int
    {
        return $this->entries()->where('is_correct', true)->count();
    }
}
