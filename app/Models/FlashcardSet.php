<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FlashcardSet extends Model
{
    use HasFactory;

    protected $fillable = [
        'learning_category_id', 'learning_chapter_id',
        'title_en', 'title_np', 'description',
        'card_count', 'is_published', 'display_order',
    ];

    protected $casts = [
        'is_published'  => 'boolean',
        'card_count'    => 'integer',
        'display_order' => 'integer',
    ];

    // ── Relationships ──────────────────────────────────────────────────────

    public function category()
    {
        return $this->belongsTo(LearningCategory::class, 'learning_category_id');
    }

    public function chapter()
    {
        return $this->belongsTo(LearningChapter::class, 'learning_chapter_id');
    }

    public function flashcards()
    {
        return $this->hasMany(Flashcard::class)->where('is_active', true)->orderBy('display_order');
    }

    public function allCards()
    {
        return $this->hasMany(Flashcard::class)->orderBy('display_order');
    }

    // ── Scopes ─────────────────────────────────────────────────────────────

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    // ── Helpers ────────────────────────────────────────────────────────────

    public function syncCardCount(): void
    {
        $this->update(['card_count' => $this->flashcards()->count()]);
    }
}
