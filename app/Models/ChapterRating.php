<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChapterRating extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'learning_chapter_id', 'rating', 'comment',
    ];

    protected $casts = [
        'rating' => 'integer',
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
