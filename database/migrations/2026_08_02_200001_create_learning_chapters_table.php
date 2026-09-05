<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('learning_chapters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('learning_category_id')->constrained()->cascadeOnDelete();
            $table->string('title_en');
            $table->string('title_np')->nullable();
            $table->longText('content_en')->nullable();
            $table->longText('content_np')->nullable();
            $table->text('summary_en')->nullable();
            $table->text('summary_np')->nullable();
            $table->string('image_url')->nullable();
            $table->string('video_url')->nullable();
            $table->unsignedSmallInteger('read_time_minutes')->default(5);
            $table->unsignedSmallInteger('display_order')->default(0);
            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('learning_chapters');
    }
};
