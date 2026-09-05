<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyQuizEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'daily_quiz_id', 'selected_index', 'is_correct', 'answered_at',
    ];

    protected $casts = [
        'is_correct'    => 'boolean',
        'selected_index'=> 'integer',
        'answered_at'   => 'datetime',
    ];

    // ── Relationships ──────────────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function dailyQuiz()
    {
        return $this->belongsTo(DailyQuiz::class);
    }
}
