<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'mock_test_id', 'competition_id',
        'is_competition', 'category',
        'total_questions', 'correct_answers', 'negative_marks',
        'final_score', 'score_percentage',
        'percentile_score', 'difficulty_breakdown',
        'passed', 'overtime', 'time_taken_seconds',
        'answers', 'completed_at',
    ];

    protected $casts = [
        'answers'             => 'array',
        'difficulty_breakdown'=> 'array',
        'passed'              => 'boolean',
        'overtime'            => 'boolean',
        'is_competition'      => 'boolean',
        'completed_at'        => 'datetime',
        'score_percentage'    => 'integer',
        'percentile_score'    => 'decimal:2',
        'negative_marks'      => 'decimal:2',
        'final_score'         => 'decimal:2',
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

    public function competition()
    {
        return $this->belongsTo(Competition::class);
    }

    public function competitionAttempt()
    {
        return $this->hasOne(CompetitionAttempt::class);
    }
}
