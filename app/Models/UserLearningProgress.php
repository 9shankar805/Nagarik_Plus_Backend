<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserLearningProgress extends Model
{
    use HasFactory;

    protected $table = 'user_learning_progress';

    protected $fillable = [
        'user_id', 'learning_chapter_id', 'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
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
}
