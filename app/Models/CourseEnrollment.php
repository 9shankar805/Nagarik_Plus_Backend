<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseEnrollment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'course_id', 'enrolled_at', 'completed_at', 'progress_pct',
    ];

    protected $casts = [
        'enrolled_at'  => 'datetime',
        'completed_at' => 'datetime',
        'progress_pct' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function recalculateProgress(): void
    {
        $course        = $this->course;
        $totalChapters = $course->total_chapters;
        if ($totalChapters === 0) return;

        $chapterIds = LearningChapter::whereIn(
            'subject_id', $course->subjects()->pluck('id')
        )->pluck('id');

        $readCount = UserLearningProgress::where('user_id', $this->user_id)
            ->whereIn('learning_chapter_id', $chapterIds)->count();

        $this->update([
            'progress_pct' => round(($readCount / $totalChapters) * 100),
            'completed_at' => $readCount >= $totalChapters ? now() : null,
        ]);
    }
}
