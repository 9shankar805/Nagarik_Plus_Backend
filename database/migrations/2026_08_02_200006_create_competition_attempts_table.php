<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('competition_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('competition_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('test_attempt_id')->constrained()->cascadeOnDelete();

            // Computed after submission
            $table->decimal('raw_score', 8, 2)->default(0);       // correct - negative
            $table->decimal('score_percentage', 5, 2)->default(0);
            $table->unsignedInteger('correct_answers')->default(0);
            $table->unsignedInteger('wrong_answers')->default(0);
            $table->unsignedInteger('unattempted')->default(0);
            $table->unsignedInteger('time_taken_seconds')->nullable();

            // Set after leaderboard computation
            $table->unsignedInteger('rank')->nullable();
            $table->decimal('prize_won', 10, 2)->default(0.00);

            $table->timestamps();

            $table->unique(['competition_id', 'user_id']);
            $table->index(['competition_id', 'score_percentage', 'time_taken_seconds'], 'comp_attempts_score_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('competition_attempts');
    }
};
