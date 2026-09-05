<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Audio extends Model
{
    protected $fillable = [
        'title',
        'artist',
        'duration_seconds',
        'cover_image_url',
        'audio_url',
        'category_id',
        'usage_count',
        'is_trending',
    ];

    protected $casts = [
        'is_trending' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(AudioCategory::class);
    }

    public function userShorts()
    {
        return $this->hasMany(UserShort::class);
    }

    public function incrementUsage()
    {
        $this->increment('usage_count');
    }

    public function scopeTrending($query)
    {
        return $query->where('is_trending', true)->orderBy('usage_count', 'desc');
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('artist', 'like', "%{$search}%");
        });
    }
}
