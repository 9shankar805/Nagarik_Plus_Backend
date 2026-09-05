<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('video_classes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('learning_category_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('video_url')->nullable();
            $table->string('thumbnail_url')->nullable();
            $table->integer('duration_minutes')->nullable();
            $table->boolean('is_live')->default(false);
            $table->timestamp('live_scheduled_at')->nullable();
            $table->enum('status', ['scheduled', 'live', 'recorded', 'inactive'])->default('recorded');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('video_classes');
    }
};
