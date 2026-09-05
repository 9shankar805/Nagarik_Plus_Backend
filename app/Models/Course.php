<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'program_id', 'title_en', 'title_np',
        'description_en', 'description_np', 'thumbnail_url',
        'display_order', 'total_subjects', 'total_chapters', 'is_published',
    ];

    protected $casts = [
        'is_published'   => 'boolean',
        'display_order'  => 'integer',
        'total_subjects' => 'integer',
        'total_chapters' => 'integer',
    ];

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function subjects()
    {
        return $this->hasMany(Subject::class)->orderBy('display_order');
    }

    public function enrollments()
    {
        return $this->hasMany(CourseEnrollment::class);
    }

    public function mockTests()
    {
        // mock tests whose category matches this course's program category
        return $this->hasManyThrough(MockTest::class, Program::class,
            'id', 'learning_category_id', 'program_id', 'learning_category_id');
    }

    public function liveSessions()
    {
        return $this->hasMany(LiveSession::class);
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function syncCounts(): void
    {
        $this->update([
            'total_subjects' => $this->subjects()->count(),
            'total_chapters' => LearningChapter::whereIn(
                'subject_id', $this->subjects()->pluck('id')
            )->count(),
        ]);
    }
}
