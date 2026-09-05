<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. learning_chapters — add content_type + duration_minutes
        Schema::table('learning_chapters', function (Blueprint $table) {
            // lecture = live/recorded class, video = standalone video, note = text note,
            // model_set = practice paper, audio = audio lesson
            $table->enum('content_type', ['lecture', 'video', 'note', 'model_set', 'audio'])
                  ->default('note')
                  ->after('video_url')
                  ->comment('Type of content in this chapter');

            // video duration for this specific chapter
            $table->unsignedSmallInteger('duration_minutes')->nullable()
                  ->after('content_type')
                  ->comment('Length of video/lecture in minutes');

            // which subject this chapter belongs to (if used in Program→Course→Subject flow)
            // already exists as subject_id from previous migration — skip
        });

        // 2. subjects — add video_count, note_count, model_set_count, audio_count
        Schema::table('subjects', function (Blueprint $table) {
            $table->unsignedSmallInteger('video_count')    ->default(0)->after('chapter_count');
            $table->unsignedSmallInteger('note_count')     ->default(0)->after('video_count');
            $table->unsignedSmallInteger('model_set_count')->default(0)->after('note_count');
            $table->unsignedSmallInteger('audio_count')    ->default(0)->after('model_set_count');
        });

        // 3. programs — add aggregate stats
        Schema::table('programs', function (Blueprint $table) {
            $table->unsignedInteger('total_lectures')      ->default(0)->after('enrolled_count');
            $table->unsignedInteger('total_videos')        ->default(0)->after('total_lectures');
            $table->unsignedInteger('total_video_minutes') ->default(0)->after('total_videos');
            $table->unsignedInteger('total_notes')         ->default(0)->after('total_video_minutes');
            $table->unsignedInteger('total_model_sets')    ->default(0)->after('total_notes');
            $table->unsignedInteger('total_audio')         ->default(0)->after('total_model_sets');
            // guru count = number of distinct instructors (stored as JSON array of names)
            $table->json('guru_names')->nullable()->after('total_audio');
        });
    }

    public function down(): void
    {
        Schema::table('learning_chapters', function (Blueprint $table) {
            $table->dropColumn(['content_type', 'duration_minutes']);
        });
        Schema::table('subjects', function (Blueprint $table) {
            $table->dropColumn(['video_count', 'note_count', 'model_set_count', 'audio_count']);
        });
        Schema::table('programs', function (Blueprint $table) {
            $table->dropColumn([
                'total_lectures', 'total_videos', 'total_video_minutes',
                'total_notes', 'total_model_sets', 'total_audio', 'guru_names',
            ]);
        });
    }
};
