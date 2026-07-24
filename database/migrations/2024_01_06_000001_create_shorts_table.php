<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('shorts')) {
            Schema::create('shorts', function (Blueprint $table) {
                $table->id();
                $table->string('title_en');
                $table->string('title_np')->nullable();
                $table->text('description_en')->nullable();
                $table->text('description_np')->nullable();
                $table->string('video_url');
                $table->string('thumbnail_url')->nullable();
                $table->string('category')->default('traffic_rules');
                $table->integer('duration_seconds')->default(30);
                $table->integer('views_count')->default(0);
                $table->integer('likes_count')->default(0);
                $table->boolean('is_published')->default(true);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('shorts');
    }
};
