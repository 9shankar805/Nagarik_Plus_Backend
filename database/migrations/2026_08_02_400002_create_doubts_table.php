<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Q&A / Doubt system
        Schema::create('doubts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('learning_category_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('subject_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('learning_chapter_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->text('body');
            $table->string('image_url')->nullable();
            // status: open | answered | closed
            $table->string('status')->default('open');
            $table->unsignedSmallInteger('upvotes')->default(0);
            $table->unsignedSmallInteger('answers_count')->default(0);
            $table->boolean('is_pinned')->default(false);
            $table->timestamps();

            $table->index(['learning_category_id', 'status']);
            $table->index(['user_id', 'status']);
        });

        // Doubt answers
        Schema::create('doubt_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doubt_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('body');
            $table->string('image_url')->nullable();
            $table->boolean('is_accepted')->default(false);  // questioner accepts best answer
            $table->boolean('is_instructor')->default(false); // answered by instructor/admin
            $table->unsignedSmallInteger('upvotes')->default(0);
            $table->timestamps();
        });

        // Doubt upvotes (so users can't double-vote)
        Schema::create('doubt_upvotes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->morphs('votable'); // doubt or doubt_answer
            $table->timestamps();

            $table->unique(['user_id', 'votable_id', 'votable_type'], 'doubt_upvotes_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('doubt_upvotes');
        Schema::dropIfExists('doubt_answers');
        Schema::dropIfExists('doubts');
    }
};
