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
        Schema::create('effects', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->foreignId('category_id')->nullable();
            $table->string('thumbnail_url');
            $table->string('deepar_file_url');
            $table->integer('file_size_kb');
            $table->boolean('is_trending')->default(false);
            $table->integer('usage_count')->default(0);
            $table->timestamps();
            
            $table->index('category_id');
            $table->index('is_trending');
            $table->index('usage_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('effects');
    }
};
