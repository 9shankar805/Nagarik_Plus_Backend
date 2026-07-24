<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Document reminders
        Schema::create('reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('document_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('due_date');
            $table->integer('days_before')->default(30);
            $table->boolean('is_enabled')->default(true);
            $table->timestamp('last_notified_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'due_date']);
        });

        // Office locations
        Schema::create('offices', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('name_np')->nullable();
            $table->string('category'); // passport, transport, tax, municipality, police, ward
            $table->string('address');
            $table->string('district');
            $table->string('province');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->string('office_hours')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['category', 'district']);
        });

        // Citizen news & public notices
        Schema::create('news', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('title_np')->nullable();
            $table->text('content');
            $table->text('content_np')->nullable();
            $table->string('category'); // notice, service, exam, deadline, update
            $table->string('source');
            $table->string('source_url')->nullable();
            $table->string('image_url')->nullable();
            $table->boolean('is_verified')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_published')->default(true);
            $table->timestamp('published_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->index(['category', 'is_published']);
        });

        // Emergency contacts
        Schema::create('emergency_contacts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('name_np')->nullable();
            $table->string('number');
            $table->string('description')->nullable();
            $table->string('category'); // police, ambulance, fire, disaster, health
            $table->string('icon')->nullable();
            $table->string('color')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // AI chat history
        Schema::create('ai_conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('user_message');
            $table->text('ai_response');
            $table->string('intent')->nullable(); // detected intent
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
        });

        // Push notification tokens
        Schema::create('device_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('token');
            $table->string('platform')->default('android'); // android, ios
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'token']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('device_tokens');
        Schema::dropIfExists('ai_conversations');
        Schema::dropIfExists('emergency_contacts');
        Schema::dropIfExists('news');
        Schema::dropIfExists('offices');
        Schema::dropIfExists('reminders');
    }
};
