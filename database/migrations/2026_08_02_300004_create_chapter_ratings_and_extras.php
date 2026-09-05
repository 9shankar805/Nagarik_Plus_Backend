<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Chapter ratings (1-5 stars)
        Schema::create('chapter_ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('learning_chapter_id')->constrained()->cascadeOnDelete();
            $table->tinyInteger('rating');          // 1-5
            $table->text('comment')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'learning_chapter_id']);
        });

        // Link quiz questions to chapters (optional metadata)
        Schema::table('quiz_questions', function (Blueprint $table) {
            $table->foreignId('learning_chapter_id')->nullable()->after('category')
                  ->constrained()->nullOnDelete();
            $table->string('tags')->nullable()->after('learning_chapter_id'); // comma-separated
        });

        // Practice sessions (unlimited, un-timed drilling)
        Schema::create('practice_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('category');
            $table->foreignId('learning_chapter_id')->nullable()->constrained()->nullOnDelete();
            $table->string('mode')->default('all'); // all | wrong_only | bookmarked | difficulty
            $table->string('difficulty')->nullable(); // easy | medium | hard | null=all
            $table->unsignedInteger('questions_answered')->default(0);
            $table->unsignedInteger('correct_answers')->default(0);
            $table->json('answers')->nullable();
            $table->unsignedInteger('time_taken_seconds')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'category']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('practice_sessions');
        Schema::table('quiz_questions', function (Blueprint $table) {
            $table->dropForeign(['learning_chapter_id']);
            $table->dropColumn(['learning_chapter_id', 'tags']);
        });
        Schema::dropIfExists('chapter_ratings');
    }
};
