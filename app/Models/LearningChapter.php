<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LearningChapter extends Model
{
    use HasFactory;

    const TYPE_LECTURE   = 'lecture';
    const TYPE_VIDEO     = 'video';
    const TYPE_NOTE      = 'note';
    const TYPE_MODEL_SET = 'model_set';
    const TYPE_AUDIO     = 'audio';

    const TYPES = [
        'lecture'   => '🔴 Live Lecture',
        'video'     => '🎬 Recorded Video',
        'note'      => '📝 Note / Reading',
        'model_set' => '📋 Model Set / Paper',
        'audio'     => '🎧 Audio Lesson',
    ];

    protected $fillable = [
        'learning_category_id', 'subject_id',
        'title_en', 'title_np',
        'content_en', 'content_np',
        'summary_en', 'summary_np',
        'image_url', 'video_url',
        'content_type', 'duration_minutes',
        'read_time_minutes',
        'display_order', 'is_published', 'published_at',
    ];

    protected $casts = [
        'is_published'     => 'boolean',
        'published_at'     => 'datetime',
        'read_time_minutes'=> 'integer',
        'duration_minutes' => 'integer',
        'display_order'    => 'integer',
    ];

    // ── Helpers ────────────────────────────────────────────────────────────
    public function isVideo(): bool    { return in_array($this->content_type, ['video', 'lecture']); }
    public function isNote(): bool     { return $this->content_type === 'note'; }
    public function isAudio(): bool    { return $this->content_type === 'audio'; }
    public function isModelSet(): bool { return $this->content_type === 'model_set'; }

    public function typeLabel(): string
    {
        return self::TYPES[$this->content_type] ?? '📝 Note / Reading';
    }

    // ── Relationships ──────────────────────────────────────────────────────
    public function category()
    {
        return $this->belongsTo(LearningCategory::class, 'learning_category_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function readByUsers()
    {
        return $this->hasMany(UserLearningProgress::class);
    }

    public function bookmarks()
    {
        return $this->morphMany(UserBookmark::class, 'bookmarkable');
    }

    public function ratings()
    {
        return $this->hasMany(ChapterRating::class, 'learning_chapter_id');
    }

    // ── Scopes ─────────────────────────────────────────────────────────────
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order')->orderBy('created_at');
    }

    public function scopeByCategory($query, int $categoryId)
    {
        return $query->where('learning_category_id', $categoryId);
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('content_type', $type);
    }
}
