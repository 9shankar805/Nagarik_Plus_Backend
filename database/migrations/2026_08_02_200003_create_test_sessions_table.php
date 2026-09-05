<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('test_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('mock_test_id')->nullable()->constrained()->nullOnDelete();
            $table->string('session_token', 64)->unique();
            $table->json('question_ids');          // ordered array of question IDs for this session
            $table->timestamp('started_at')->useCurrent();
            $table->timestamp('expires_at')->useCurrent();
            $table->boolean('submitted')->default(false);
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'submitted']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('test_sessions');
    }
};
