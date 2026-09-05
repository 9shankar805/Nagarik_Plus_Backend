<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserShort extends Model
{
    protected $fillable = [
        'user_id',
        'audio_id',
        'effect_id',
        'video_url',
        'cover_image_url',
        'caption',
        'privacy',
        'location',
        'likes_count',
        'comments_count',
        'shares_count',
        'views_count',
        'is_processed',
        'published_at',
    ];

    protected $casts = [
        'is_processed' => 'boolean',
        'published_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function audio()
    {
        return $this->belongsTo(Audio::class);
    }

    public function effect()
    {
        return $this->belongsTo(Effect::class);
    }

    public function incrementViews()
    {
        $this->increment('views_count');
    }

    public function incrementLikes()
    {
        $this->increment('likes_count');
    }

    public function decrementLikes()
    {
        $this->decrement('likes_count');
    }
}
