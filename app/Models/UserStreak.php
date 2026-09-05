<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserStreak extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'current_streak', 'longest_streak',
        'last_activity_date', 'total_study_days',
        'daily_quiz_streak', 'last_daily_quiz_date',
    ];

    protected $casts = [
        'last_activity_date'  => 'date',
        'last_daily_quiz_date'=> 'date',
        'current_streak'      => 'integer',
        'longest_streak'      => 'integer',
        'total_study_days'    => 'integer',
        'daily_quiz_streak'   => 'integer',
    ];

    // ── Relationships ──────────────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ── Helpers ────────────────────────────────────────────────────────────

    /**
     * Record a study activity today — updates streak accordingly.
     */
    public function recordActivity(): void
    {
        $today     = today();
        $yesterday = today()->subDay();

        if ($this->last_activity_date === null) {
            // First ever activity
            $this->current_streak    = 1;
            $this->total_study_days  = 1;
        } elseif ($this->last_activity_date->equalTo($today)) {
            // Already recorded today — nothing to do
            return;
        } elseif ($this->last_activity_date->equalTo($yesterday)) {
            // Consecutive day — extend streak
            $this->current_streak++;
            $this->total_study_days++;
        } else {
            // Streak broken — restart
            $this->current_streak   = 1;
            $this->total_study_days++;
        }

        $this->longest_streak    = max($this->longest_streak, $this->current_streak);
        $this->last_activity_date = $today;
        $this->save();

        // Log daily activity record
        UserStudyActivity::firstOrCreate([
            'user_id'       => $this->user_id,
            'activity_date' => $today,
        ]);
    }

    /**
     * Record a daily quiz answer — maintains quiz streak separately.
     */
    public function recordDailyQuiz(): void
    {
        $today     = today();
        $yesterday = today()->subDay();

        if ($this->last_daily_quiz_date === null) {
            $this->daily_quiz_streak = 1;
        } elseif ($this->last_daily_quiz_date->equalTo($today)) {
            return; // Already recorded
        } elseif ($this->last_daily_quiz_date->equalTo($yesterday)) {
            $this->daily_quiz_streak++;
        } else {
            $this->daily_quiz_streak = 1;
        }

        $this->last_daily_quiz_date = $today;
        $this->save();
    }

    /**
     * Get or create streak record for a user.
     */
    public static function forUser(int $userId): self
    {
        return static::firstOrCreate(['user_id' => $userId]);
    }
}
