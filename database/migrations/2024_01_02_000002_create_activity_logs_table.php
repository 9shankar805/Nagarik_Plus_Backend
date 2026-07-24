<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type');        // document_added, reminder_triggered, guide_viewed, etc.
            $table->string('description');
            $table->string('subject_type')->nullable(); // Document, News, CitizenService, etc.
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->json('metadata')->nullable();
            $table->string('ip_address')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index(['type', 'created_at']);
        });

        Schema::create('push_notifications', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('title_np')->nullable();
            $table->text('body');
            $table->text('body_np')->nullable();
            $table->string('type')->default('announcement'); // announcement, reminder, news
            $table->string('target')->default('all'); // all, user, role
            $table->unsignedBigInteger('target_id')->nullable(); // user_id if target=user
            $table->json('data')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->integer('sent_count')->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('app_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('string'); // string, boolean, json, integer
            $table->string('group')->default('general');
            $table->string('label');
            $table->timestamps();
        });

        Schema::create('banners', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('title_np')->nullable();
            $table->text('description')->nullable();
            $table->string('image_url')->nullable();
            $table->string('link_type')->nullable(); // service, news, external, none
            $table->string('link_value')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('banners');
        Schema::dropIfExists('app_settings');
        Schema::dropIfExists('push_notifications');
        Schema::dropIfExists('activity_logs');
    }
};
