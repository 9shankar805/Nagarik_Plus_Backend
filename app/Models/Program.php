<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    use HasFactory;

    protected $fillable = [
        'learning_category_id', 'slug', 'title_en', 'title_np',
        'description_en', 'description_np', 'thumbnail_url', 'banner_url',
        'icon', 'color_code', 'price', 'is_free', 'is_published',
        'display_order', 'enrolled_count',
        'total_lectures', 'total_videos', 'total_video_minutes',
        'total_notes', 'total_model_sets', 'total_audio',
        'guru_names',
    ];

    protected $casts = [
        'is_free'              => 'boolean',
        'is_published'         => 'boolean',
        'price'                => 'decimal:2',
        'enrolled_count'       => 'integer',
        'display_order'        => 'integer',
        'total_lectures'       => 'integer',
        'total_videos'         => 'integer',
        'total_video_minutes'  => 'integer',
        'total_notes'          => 'integer',
        'total_model_sets'     => 'integer',
        'total_audio'          => 'integer',
        'guru_names'           => 'array',
    ];

    /** Recompute all aggregate stats by scanning subjects */
    public function syncStats(): void
    {
        $subjectIds = $this->courses()
            ->with('subjects')
            ->get()
            ->flatMap(fn($c) => $c->subjects->pluck('id'));

        $chapters = \App\Models\LearningChapter::whereIn('subject_id', $subjectIds)
            ->where('is_published', true)
            ->select('content_type', 'duration_minutes')
            ->get();

        $videoMins = $chapters
            ->whereIn('content_type', ['video', 'lecture'])
            ->sum('duration_minutes');

        $this->update([
            'total_lectures'      => $chapters->whereIn('content_type', ['lecture'])->count(),
            'total_videos'        => $chapters->whereIn('content_type', ['video', 'lecture'])->count(),
            'total_video_minutes' => (int) $videoMins,
            'total_notes'         => $chapters->where('content_type', 'note')->count(),
            'total_model_sets'    => $chapters->where('content_type', 'model_set')->count(),
            'total_audio'         => $chapters->where('content_type', 'audio')->count(),
        ]);
    }

    public function getVideoTimeAttribute(): string
    {
        $mins  = $this->total_video_minutes ?? 0;
        $h     = intdiv($mins, 60);
        $m     = $mins % 60;
        $s     = 0;
        return sprintf('%dh %02dm %02ds', $h, $m, $s);
    }

    public function getGuruCountAttribute(): int
    {
        return count($this->guru_names ?? []);
    }

    public function category()
    {
        return $this->belongsTo(LearningCategory::class, 'learning_category_id');
    }

    public function courses()
    {
        return $this->hasMany(Course::class)->orderBy('display_order');
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function getTotalChaptersAttribute(): int
    {
        return $this->courses->sum(fn($c) => $c->total_chapters);
    }
}
