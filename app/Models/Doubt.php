<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Doubt extends Model
{
    use HasFactory;

    const STATUS_OPEN     = 'open';
    const STATUS_ANSWERED = 'answered';
    const STATUS_CLOSED   = 'closed';

    protected $fillable = [
        'user_id', 'learning_category_id', 'subject_id', 'learning_chapter_id',
        'title', 'body', 'image_url', 'status',
        'upvotes', 'answers_count', 'is_pinned',
    ];

    protected $casts = [
        'is_pinned'     => 'boolean',
        'upvotes'       => 'integer',
        'answers_count' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(LearningCategory::class, 'learning_category_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function chapter()
    {
        return $this->belongsTo(LearningChapter::class, 'learning_chapter_id');
    }

    public function answers()
    {
        return $this->hasMany(DoubtAnswer::class)->orderByDesc('is_accepted')->latest();
    }

    public function acceptedAnswer()
    {
        return $this->hasOne(DoubtAnswer::class)->where('is_accepted', true);
    }

    public function scopeOpen($query)
    {
        return $query->where('status', self::STATUS_OPEN);
    }
}
