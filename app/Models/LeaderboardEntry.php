<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaderboardEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'competition_id', 'user_id',
        'score_percentage', 'correct_answers', 'time_taken_seconds',
        'rank', 'prize_won', 'computed_at',
    ];

    protected $casts = [
        'score_percentage'   => 'decimal:2',
        'prize_won'          => 'decimal:2',
        'correct_answers'    => 'integer',
        'time_taken_seconds' => 'integer',
        'rank'               => 'integer',
        'computed_at'        => 'datetime',
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
}
