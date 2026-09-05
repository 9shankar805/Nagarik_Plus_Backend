<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quiz_questions', function (Blueprint $table) {
            // Topic within a category (e.g. "Kinematics", "Constitutional Law")
            // Used for radar-chart topic breakdown and weak-area analytics
            $table->string('topic_id', 100)->nullable()->after('category')
                  ->comment('Sub-topic slug within the category, e.g. "kinematics", "fundamental_rights"');

            // ELO-style difficulty weight (1.0 = normal, 2.0 = hard earns double weighted points)
            // Used by the Adaptive Mock Test algorithm for weighted scoring
            $table->decimal('difficulty_weight', 4, 2)->default(1.00)->after('difficulty')
                  ->comment('ELO weight: easy=0.5, medium=1.0, hard=2.0');
        });

        // Seed sensible defaults based on existing difficulty values
        \DB::statement("UPDATE quiz_questions SET difficulty_weight = CASE
            WHEN difficulty = 'easy'   THEN 0.50
            WHEN difficulty = 'medium' THEN 1.00
            WHEN difficulty = 'hard'   THEN 2.00
            ELSE 1.00
        END");
    }

    public function down(): void
    {
        Schema::table('quiz_questions', function (Blueprint $table) {
            $table->dropColumn(['topic_id', 'difficulty_weight']);
        });
    }
};
