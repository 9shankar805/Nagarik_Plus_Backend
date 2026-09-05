<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Pivot: allows assigning specific questions to a mock test
        // When this table has rows for a test, those questions are used instead of random pool
        Schema::create('mock_test_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mock_test_id')
                  ->constrained('mock_tests')
                  ->cascadeOnDelete();
            $table->foreignId('quiz_question_id')
                  ->constrained('quiz_questions')
                  ->cascadeOnDelete();
            $table->unsignedSmallInteger('display_order')->default(0);
            $table->timestamps();

            $table->unique(['mock_test_id', 'quiz_question_id']);
            $table->index('mock_test_id');
        });

        // Add a flag on mock_tests so the engine knows which mode to use
        Schema::table('mock_tests', function (Blueprint $table) {
            $table->boolean('use_fixed_questions')->default(false)->after('is_featured');
        });
    }

    public function down(): void
    {
        Schema::table('mock_tests', function (Blueprint $table) {
            $table->dropColumn('use_fixed_questions');
        });
        Schema::dropIfExists('mock_test_questions');
    }
};
