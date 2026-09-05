<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('live_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('subject_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('learning_category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title_en');
            $table->string('title_np')->nullable();
            $table->text('description')->nullable();
            $table->string('instructor_name')->nullable();
            $table->string('instructor_avatar')->nullable();
            // type: live | recorded | doubt_clearing
            $table->string('type')->default('live');
            $table->string('stream_url')->nullable();       // YouTube/Zoom/custom RTMP
            $table->string('recording_url')->nullable();    // set after live ends
            $table->string('thumbnail_url')->nullable();
            $table->timestamp('starts_at')->useCurrent();
            $table->unsignedSmallInteger('duration_minutes')->default(60);
            // status: scheduled | live | ended | cancelled
            $table->string('status')->default('scheduled');
            $table->boolean('is_free')->default(true);
            $table->unsignedInteger('viewer_count')->default(0);
            $table->unsignedSmallInteger('display_order')->default(0);
            $table->timestamps();

            $table->index(['status', 'starts_at']);
            $table->index(['learning_category_id', 'starts_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('live_sessions');
    }
};
