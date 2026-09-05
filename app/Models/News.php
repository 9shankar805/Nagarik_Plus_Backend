<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    use HasFactory;

    public const STATUS_PENDING  = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'user_id',
        'title', 'title_np', 'content', 'content_np',
        'category', 'source', 'source_url', 'image_url',
        'images', 'video_url', 'video_thumbnail', 'media_type',
        'is_verified', 'is_featured', 'is_published',
        'status', 'is_short',
        'published_at', 'expires_at',
        'rejection_reason',
        'reviewed_at', 'reviewed_by',
    ];

    protected $casts = [
        'is_verified'  => 'boolean',
        'is_featured'  => 'boolean',
        'is_published' => 'boolean',
        'is_short'     => 'boolean',
        'published_at' => 'datetime',
        'expires_at'   => 'datetime',
        'reviewed_at'  => 'datetime',
        'images'       => 'array',
    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeRejected($query)
    {
        return $query->where('status', self::STATUS_REJECTED);
    }

    public function scopePublished($query)
    {
        return $query->approved()
                     ->where('is_published', true)
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
        return $query->where('is_short', true)->whereNotNull('image_url');
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

    public function approve(?int $adminId = null): bool
    {
        return $this->forceFill([
            'status'         => self::STATUS_APPROVED,
            'is_published'   => true,
            'published_at'   => $this->published_at ?? now(),
            'reviewed_at'    => now(),
            'reviewed_by'    => $adminId,
            'rejection_reason' => null,
        ])->save();
    }

    public function reject(string $reason, ?int $adminId = null): bool
    {
        return $this->forceFill([
            'status'           => self::STATUS_REJECTED,
            'is_published'     => false,
            'rejection_reason' => $reason,
            'reviewed_at'      => now(),
            'reviewed_by'      => $adminId,
        ])->save();
    }

    public function sendBackToPending(): bool
    {
        return $this->forceFill([
            'status'           => self::STATUS_PENDING,
            'rejection_reason' => null,
            'reviewed_at'      => null,
            'reviewed_by'      => null,
        ])->save();
    }
}
