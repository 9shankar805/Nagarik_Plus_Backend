<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('test_sessions', function (Blueprint $table) {
            // JSON blob storing adaptive session state:
            // {
            //   "answers": [{"question_id":1,"selected":2,"is_correct":true,"difficulty":"easy","weight":0.5,"topic_id":"kinematics"}],
            //   "elo_score": 1200.5,
            //   "current_difficulty": "medium",
            //   "asked_ids": [1, 5, 12],
            //   "is_adaptive": true
            // }
            $table->json('adaptive_state')->nullable()->after('question_ids');
        });
    }

    public function down(): void
    {
        Schema::table('test_sessions', function (Blueprint $table) {
            $table->dropColumn('adaptive_state');
        });
    }
};
