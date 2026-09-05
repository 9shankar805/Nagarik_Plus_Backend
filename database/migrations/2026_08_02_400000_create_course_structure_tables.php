<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Programs (top-level grouping) ──────────────────────────────────
        // e.g. "Driving License", "Loksewa Preparation", "Banking Exam"
        Schema::create('programs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('learning_category_id')->constrained()->cascadeOnDelete();
            $table->string('slug')->unique();
            $table->string('title_en');
            $table->string('title_np')->nullable();
            $table->text('description_en')->nullable();
            $table->text('description_np')->nullable();
            $table->string('thumbnail_url')->nullable();
            $table->string('banner_url')->nullable();
            $table->string('icon')->nullable();
            $table->string('color_code')->nullable();
            $table->decimal('price', 10, 2)->default(0.00);       // 0 = free
            $table->boolean('is_free')->default(true);
            $table->boolean('is_published')->default(false);
            $table->unsignedSmallInteger('display_order')->default(0);
            $table->unsignedInteger('enrolled_count')->default(0);
            $table->timestamps();
        });

        // ── Courses (within a program) ─────────────────────────────────────
        // e.g. "Kharidar Level", "Nayab Subba Level", "Category B License"
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->constrained()->cascadeOnDelete();
            $table->string('title_en');
            $table->string('title_np')->nullable();
            $table->text('description_en')->nullable();
            $table->text('description_np')->nullable();
            $table->string('thumbnail_url')->nullable();
            $table->unsignedSmallInteger('display_order')->default(0);
            $table->unsignedSmallInteger('total_subjects')->default(0);
            $table->unsignedSmallInteger('total_chapters')->default(0);
            $table->boolean('is_published')->default(false);
            $table->timestamps();
        });

        // ── Subjects (within a course) ─────────────────────────────────────
        // e.g. "General Knowledge", "Nepali Language", "Math & Reasoning"
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->string('title_en');
            $table->string('title_np')->nullable();
            $table->text('description_en')->nullable();
            $table->string('icon')->nullable();
            $table->string('color_code')->nullable();
            $table->unsignedSmallInteger('display_order')->default(0);
            $table->unsignedSmallInteger('chapter_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Link existing learning_chapters to subjects (optional — adds subject context)
        Schema::table('learning_chapters', function (Blueprint $table) {
            $table->foreignId('subject_id')->nullable()->after('learning_category_id')
                  ->constrained()->nullOnDelete();
        });

        // ── User course enrollments ─────────────────────────────────────────
        Schema::create('course_enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->timestamp('enrolled_at')->useCurrent();
            $table->timestamp('completed_at')->nullable();
            $table->unsignedTinyInteger('progress_pct')->default(0);
            $table->timestamps();

            $table->unique(['user_id', 'course_id']);
        });
    }

    public function down(): void
    {
        Schema::table('learning_chapters', function (Blueprint $table) {
            $table->dropForeign(['subject_id']);
            $table->dropColumn('subject_id');
        });
        Schema::dropIfExists('course_enrollments');
        Schema::dropIfExists('subjects');
        Schema::dropIfExists('courses');
        Schema::dropIfExists('programs');
    }
};
