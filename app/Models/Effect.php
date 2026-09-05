<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Effect extends Model
{
    protected $fillable = [
        'title',
        'category_id',
        'thumbnail_url',
        'deepar_file_url',
        'file_size_kb',
        'is_trending',
        'usage_count',
    ];

    protected $casts = [
        'is_trending' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(EffectCategory::class);
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
}
