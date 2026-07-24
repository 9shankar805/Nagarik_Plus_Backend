<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Short extends Model
{
    use HasFactory;

    protected $fillable = [
        'title_en',
        'title_np',
        'description_en',
        'description_np',
        'video_url',
        'thumbnail_url',
        'category',
        'duration_seconds',
        'views_count',
        'likes_count',
        'is_published',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'duration_seconds' => 'integer',
        'views_count' => 'integer',
        'likes_count' => 'integer',
    ];
}
