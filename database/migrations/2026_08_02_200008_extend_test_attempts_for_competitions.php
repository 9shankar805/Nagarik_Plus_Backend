<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('test_attempts', function (Blueprint $table) {
            $table->foreignId('competition_id')->nullable()->after('mock_test_id')
                  ->constrained()->nullOnDelete();
            $table->boolean('is_competition')->default(false)->after('competition_id');
            $table->decimal('negative_marks', 8, 2)->default(0)->after('correct_answers');
            $table->decimal('final_score', 8, 2)->default(0)->after('negative_marks');
            $table->boolean('overtime')->default(false)->after('passed');
        });
    }

    public function down(): void
    {
        Schema::table('test_attempts', function (Blueprint $table) {
            $table->dropForeign(['competition_id']);
            $table->dropColumn(['competition_id', 'is_competition', 'negative_marks', 'final_score', 'overtime']);
        });
    }
};
