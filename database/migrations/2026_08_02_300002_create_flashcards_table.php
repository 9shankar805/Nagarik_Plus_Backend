<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('flashcard_sets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('learning_category_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('learning_chapter_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title_en');
            $table->string('title_np')->nullable();
            $table->text('description')->nullable();
            $table->unsignedSmallInteger('card_count')->default(0);
            $table->boolean('is_published')->default(false);
            $table->unsignedSmallInteger('display_order')->default(0);
            $table->timestamps();
        });

        Schema::create('flashcards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('flashcard_set_id')->constrained()->cascadeOnDelete();
            $table->text('front_en');           // term / question side
            $table->text('front_np')->nullable();
            $table->text('back_en');            // definition / answer side
            $table->text('back_np')->nullable();
            $table->string('image_url')->nullable();
            $table->unsignedSmallInteger('display_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Spaced repetition — user's familiarity with each card
        Schema::create('user_flashcard_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('flashcard_id')->constrained()->cascadeOnDelete();
            // 0=new 1=learning 2=reviewing 3=mastered
            $table->tinyInteger('status')->default(0);
            $table->unsignedSmallInteger('times_seen')->default(0);
            $table->unsignedSmallInteger('times_correct')->default(0);
            $table->timestamp('next_review_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'flashcard_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_flashcard_progress');
        Schema::dropIfExists('flashcards');
        Schema::dropIfExists('flashcard_sets');
    }
};
