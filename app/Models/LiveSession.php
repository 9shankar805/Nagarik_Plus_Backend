<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LiveSession extends Model
{
    use HasFactory;

    const TYPE_LIVE            = 'live';
    const TYPE_RECORDED        = 'recorded';
    const TYPE_DOUBT_CLEARING  = 'doubt_clearing';

    const STATUS_SCHEDULED = 'scheduled';
    const STATUS_LIVE      = 'live';
    const STATUS_ENDED     = 'ended';
    const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'course_id', 'subject_id', 'learning_category_id',
        'title_en', 'title_np', 'description',
        'instructor_name', 'instructor_avatar',
        'type', 'stream_url', 'recording_url', 'thumbnail_url',
        'starts_at', 'duration_minutes', 'status',
        'is_free', 'viewer_count', 'display_order',
    ];

    protected $casts = [
        'starts_at'        => 'datetime',
        'is_free'          => 'boolean',
        'viewer_count'     => 'integer',
        'duration_minutes' => 'integer',
        'display_order'    => 'integer',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function category()
    {
        return $this->belongsTo(LearningCategory::class, 'learning_category_id');
    }

    public function scopeUpcoming($query)
    {
        return $query->where('status', self::STATUS_SCHEDULED)
                     ->where('starts_at', '>', now())
                     ->orderBy('starts_at');
    }

    public function scopeLive($query)
    {
        return $query->where('status', self::STATUS_LIVE);
    }

    public function scopeRecorded($query)
    {
        return $query->where('status', self::STATUS_ENDED)
                     ->whereNotNull('recording_url');
    }

    public function getEndsAtAttribute()
    {
        return $this->starts_at->addMinutes($this->duration_minutes);
    }
}
