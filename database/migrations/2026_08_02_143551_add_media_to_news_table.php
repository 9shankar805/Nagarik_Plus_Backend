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
        Schema::table('news', function (Blueprint $table) {
            $table->json('images')->nullable()->after('image_url');
            $table->string('video_url')->nullable()->after('images');
            $table->string('video_thumbnail')->nullable()->after('video_url');
            $table->string('media_type')->default('none')->after('video_thumbnail'); // none, image, video, mixed
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('news', function (Blueprint $table) {
            $table->dropColumn(['images', 'video_url', 'video_thumbnail', 'media_type']);
        });
    }
};
