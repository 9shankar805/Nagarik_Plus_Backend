<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // News likes
        Schema::create('news_likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('news_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['user_id', 'news_id']);
        });

        // News bookmarks
        Schema::create('news_bookmarks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('news_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['user_id', 'news_id']);
        });

        // News comments
        Schema::create('news_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('news_id')->constrained()->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('news_comments')->cascadeOnDelete();
            $table->text('content');
            $table->timestamps();
            $table->index(['news_id', 'created_at']);
        });

        // News shares
        Schema::create('news_shares', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('news_id')->constrained()->cascadeOnDelete();
            $table->string('platform')->nullable(); // social platform: facebook, whatsapp, etc.
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('news_shares');
        Schema::dropIfExists('news_comments');
        Schema::dropIfExists('news_bookmarks');
        Schema::dropIfExists('news_likes');
    }
};
