<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Admin publishes one question per day per category (or global)
        Schema::create('daily_quizzes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_question_id')->constrained()->cascadeOnDelete();
            $table->foreignId('learning_category_id')->nullable()->constrained()->nullOnDelete();
            $table->date('quiz_date')->index();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['quiz_date', 'learning_category_id'], 'daily_quiz_date_cat_unique');
        });

        // User answers to daily quiz
        Schema::create('daily_quiz_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('daily_quiz_id')->constrained()->cascadeOnDelete();
            $table->tinyInteger('selected_index');   // -1 = skipped
            $table->boolean('is_correct')->default(false);
            $table->timestamp('answered_at')->useCurrent();
            $table->timestamps();

            $table->unique(['user_id', 'daily_quiz_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_quiz_entries');
        Schema::dropIfExists('daily_quizzes');
    }
};
