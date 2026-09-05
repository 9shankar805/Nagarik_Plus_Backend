<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MockTest extends Model
{
    use HasFactory;

    protected $fillable = [
        'learning_category_id',
        'title', 'title_np',
        'description', 'description_np',
        'category', 'question_count',
        'duration_minutes', 'pass_percentage', 'is_active',
        'negative_marking', 'negative_value',
        'is_featured', 'use_fixed_questions', 'created_by',
    ];

    protected $casts = [
        'is_active'            => 'boolean',
        'is_featured'          => 'boolean',
        'negative_marking'     => 'boolean',
        'negative_value'       => 'decimal:2',
        'use_fixed_questions'  => 'boolean',
    ];

    // ── Relationships ──────────────────────────────────────────────────────

    public function learningCategory()
    {
        return $this->belongsTo(LearningCategory::class, 'learning_category_id');
    }

    public function attempts()
    {
        return $this->hasMany(TestAttempt::class);
    }

    public function competitions()
    {
        return $this->hasMany(Competition::class);
    }

    public function sessions()
    {
        return $this->hasMany(TestSession::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Fixed question set (pivot)
    public function fixedQuestions()
    {
        return $this->belongsToMany(QuizQuestion::class, 'mock_test_questions', 'mock_test_id', 'quiz_question_id')
                    ->withPivot('display_order')
                    ->orderBy('mock_test_questions.display_order');
    }

    // ── Scopes ─────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    // ── Helpers ────────────────────────────────────────────────────────────

    /**
     * Calculate final score with optional negative marking.
     * Returns [final_score, negative_marks]
     */
    public function calculateScore(int $correct, int $wrong): array
    {
        $negativeMarks = 0;
        if ($this->negative_marking) {
            $negativeMarks = round($wrong * (float) $this->negative_value, 2);
        }
        $finalScore = max(0, $correct - $negativeMarks);
        return [$finalScore, $negativeMarks];
    }
}
