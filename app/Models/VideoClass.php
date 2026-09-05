<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VideoClass extends Model
{
    use HasFactory;

    protected $fillable = [
        'learning_category_id',
        'title',
        'description',
        'video_url',
        'thumbnail_url',
        'duration_minutes',
        'is_live',
        'live_scheduled_at',
        'status',
    ];

    protected $casts = [
        'is_live' => 'boolean',
        'live_scheduled_at' => 'datetime',
        'duration_minutes' => 'integer',
    ];

    public function category()
    {
        return $this->belongsTo(LearningCategory::class, 'learning_category_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', '!=', 'inactive');
    }
}
