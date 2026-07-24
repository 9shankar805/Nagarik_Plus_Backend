<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'title_np', 'content', 'content_np',
        'category', 'source', 'source_url', 'image_url',
        'is_verified', 'is_featured', 'is_published',
        'published_at', 'expires_at',
    ];

    protected $casts = [
        'is_verified'  => 'boolean',
        'is_featured'  => 'boolean',
        'is_published' => 'boolean',
        'published_at' => 'datetime',
        'expires_at'   => 'datetime',
    ];

    public function scopePublished($query)
    {
        return $query->where('is_published', true)
                     ->where(function ($q) {
                         $q->whereNull('expires_at')
                           ->orWhere('expires_at', '>', now());
                     });
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeShorts($query)
    {
        return $query->where('is_featured', true)->whereNotNull('image_url');
    }

    public function likes()
    {
        return $this->hasMany(NewsLike::class);
    }

    public function bookmarks()
    {
        return $this->hasMany(NewsBookmark::class);
    }

    public function comments()
    {
        return $this->hasMany(NewsComment::class)->whereNull('parent_id');
    }

    public function shares()
    {
        return $this->hasMany(NewsShare::class);
    }

    public function isLikedBy($userId)
    {
        return $this->likes()->where('user_id', $userId)->exists();
    }

    public function isBookmarkedBy($userId)
    {
        return $this->bookmarks()->where('user_id', $userId)->exists();
    }
}
