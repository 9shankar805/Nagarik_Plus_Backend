<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompetitionAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'competition_id', 'user_id', 'test_attempt_id',
        'raw_score', 'score_percentage',
        'correct_answers', 'wrong_answers', 'unattempted',
        'time_taken_seconds', 'rank', 'prize_won',
    ];

    protected $casts = [
        'raw_score'          => 'decimal:2',
        'score_percentage'   => 'decimal:2',
        'prize_won'          => 'decimal:2',
        'correct_answers'    => 'integer',
        'wrong_answers'      => 'integer',
        'unattempted'        => 'integer',
        'time_taken_seconds' => 'integer',
        'rank'               => 'integer',
    ];

    // ── Relationships ──────────────────────────────────────────────────────

    public function competition()
    {
        return $this->belongsTo(Competition::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function testAttempt()
    {
        return $this->belongsTo(TestAttempt::class);
    }
}
