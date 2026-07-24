<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Question bank
        Schema::create('quiz_questions', function (Blueprint $table) {
            $table->id();
            $table->string('category')->default('driving_license');
            $table->string('difficulty')->default('medium'); // easy, medium, hard
            $table->text('question');
            $table->text('question_np')->nullable();
            $table->json('options'); // ["A text","B text","C text","D text"]
            $table->json('options_np')->nullable();
            $table->tinyInteger('correct_index'); // 0-3
            $table->text('explanation')->nullable();
            $table->text('explanation_np')->nullable();
            $table->string('image_url')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Mock tests
        Schema::create('mock_tests', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('category')->default('driving_license');
            $table->integer('question_count')->default(25);
            $table->integer('duration_minutes')->default(30);
            $table->integer('pass_percentage')->default(60);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // User test attempts
        Schema::create('test_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('mock_test_id')->nullable()->constrained()->nullOnDelete();
            $table->string('category')->default('driving_license');
            $table->integer('total_questions');
            $table->integer('correct_answers')->default(0);
            $table->integer('score_percentage')->default(0);
            $table->boolean('passed')->default(false);
            $table->integer('time_taken_seconds')->nullable();
            $table->json('answers')->nullable(); // {question_id: selected_index}
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        // Road signs
        Schema::create('road_signs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('name_np')->nullable();
            $table->text('meaning');
            $table->text('meaning_np')->nullable();
            $table->string('category'); // mandatory, warning, informatory
            $table->string('image_url')->nullable();
            $table->string('color_code')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('test_attempts');
        Schema::dropIfExists('mock_tests');
        Schema::dropIfExists('quiz_questions');
        Schema::dropIfExists('road_signs');
    }
};
