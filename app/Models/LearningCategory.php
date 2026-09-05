<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LearningCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug', 'name_en', 'name_np', 'description_en', 'description_np',
        'icon', 'color_code', 'banner_url', 'display_order', 'is_active',
    ];

    protected $casts = [
        'is_active'     => 'boolean',
        'display_order' => 'integer',
    ];

    // ── Relationships ──────────────────────────────────────────────────────

    public function chapters()
    {
        return $this->hasMany(LearningChapter::class);
    }

    public function publishedChapters()
    {
        return $this->hasMany(LearningChapter::class)->where('is_published', true)->orderBy('display_order');
    }

    public function mockTests()
    {
        return $this->hasMany(MockTest::class);
    }

    public function competitions()
    {
        return $this->hasMany(Competition::class);
    }

    // ── Scopes ─────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order')->orderBy('name_en');
    }

    // ── Helpers ────────────────────────────────────────────────────────────

    public function getChapterCountAttribute(): int
    {
        return $this->chapters()->where('is_published', true)->count();
    }

    public function getMockTestCountAttribute(): int
    {
        return $this->mockTests()->where('is_active', true)->count();
    }
}
