<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id', 'title_en', 'title_np', 'description_en',
        'icon', 'color_code', 'display_order',
        'chapter_count', 'video_count', 'note_count', 'model_set_count', 'audio_count',
        'is_active',
    ];

    protected $casts = [
        'is_active'        => 'boolean',
        'chapter_count'    => 'integer',
        'video_count'      => 'integer',
        'note_count'       => 'integer',
        'model_set_count'  => 'integer',
        'audio_count'      => 'integer',
        'display_order'    => 'integer',
    ];

    /** Re-compute all content-type counts from live chapter data */
    public function syncAllCounts(): void
    {
        $chapters = $this->publishedChapters()->select('content_type')->get();

        $this->update([
            'chapter_count'   => $chapters->count(),
            'video_count'     => $chapters->whereIn('content_type', ['video', 'lecture'])->count(),
            'note_count'      => $chapters->where('content_type', 'note')->count(),
            'model_set_count' => $chapters->where('content_type', 'model_set')->count(),
            'audio_count'     => $chapters->where('content_type', 'audio')->count(),
        ]);
    }

    /** Legacy alias kept for backward compat */
    public function syncChapterCount(): void { $this->syncAllCounts(); }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function chapters()
    {
        return $this->hasMany(LearningChapter::class)->orderBy('display_order');
    }

    public function publishedChapters()
    {
        return $this->hasMany(LearningChapter::class)
            ->where('is_published', true)->orderBy('display_order');
    }

    public function questions()
    {
        return $this->hasMany(QuizQuestion::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
