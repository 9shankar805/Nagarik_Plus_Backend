<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_learning_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('learning_chapter_id')->constrained()->cascadeOnDelete();
            $table->timestamp('read_at')->useCurrent();
            $table->timestamps();

            $table->unique(['user_id', 'learning_chapter_id']);
        });

        Schema::create('user_bookmarks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->morphs('bookmarkable'); // bookmarkable_id + bookmarkable_type
            $table->timestamps();

            $table->unique(['user_id', 'bookmarkable_id', 'bookmarkable_type'], 'user_bookmarks_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_bookmarks');
        Schema::dropIfExists('user_learning_progress');
    }
};
