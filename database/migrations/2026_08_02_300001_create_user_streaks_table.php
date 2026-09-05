<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_streaks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->unsignedInteger('current_streak')->default(0);   // consecutive study days
            $table->unsignedInteger('longest_streak')->default(0);
            $table->date('last_activity_date')->nullable();           // last day user studied
            $table->unsignedInteger('total_study_days')->default(0);
            $table->unsignedInteger('daily_quiz_streak')->default(0); // consecutive daily quiz
            $table->date('last_daily_quiz_date')->nullable();
            $table->timestamps();
        });

        // Per-day activity log (used to compute streak)
        Schema::create('user_study_activity', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('activity_date');
            $table->unsignedSmallInteger('questions_answered')->default(0);
            $table->unsignedSmallInteger('chapters_read')->default(0);
            $table->unsignedSmallInteger('tests_taken')->default(0);
            $table->unsignedSmallInteger('minutes_studied')->default(0);
            $table->timestamps();

            $table->unique(['user_id', 'activity_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_study_activity');
        Schema::dropIfExists('user_streaks');
    }
};
