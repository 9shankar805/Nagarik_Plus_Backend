<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('test_attempts', function (Blueprint $table) {
            // Global percentile rank against all users who attempted the same mock test
            $table->decimal('percentile_score', 5, 2)->nullable()->after('score_percentage')
                  ->comment('Percentile rank: 80 means scored better than 80% of all takers');

            // JSON breakdown of performance per difficulty
            // e.g. {"easy":{"correct":5,"total":8},"medium":{"correct":3,"total":10},"hard":{"correct":1,"total":7}}
            $table->json('difficulty_breakdown')->nullable()->after('percentile_score');
        });
    }

    public function down(): void
    {
        Schema::table('test_attempts', function (Blueprint $table) {
            $table->dropColumn(['percentile_score', 'difficulty_breakdown']);
        });
    }
};
