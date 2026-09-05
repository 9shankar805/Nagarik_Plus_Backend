<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Achievement extends Model
{
    use HasFactory;

    // Types
    const TYPE_STREAK      = 'streak';
    const TYPE_QUIZ        = 'quiz';
    const TYPE_TEST        = 'test';
    const TYPE_CHAPTER     = 'chapter';
    const TYPE_COMPETITION = 'competition';
    const TYPE_DAILY       = 'daily';
    const TYPE_SPECIAL     = 'special';

    protected $fillable = [
        'slug', 'title_en', 'title_np',
        'description_en', 'description_np',
        'icon', 'badge_color', 'type', 'threshold', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'threshold' => 'integer',
    ];

    // ── Relationships ──────────────────────────────────────────────────────

    public function userAchievements()
    {
        return $this->hasMany(UserAchievement::class);
    }

    // ── Scopes ─────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }
}
