<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Advisor categories
        Schema::create('advisor_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('name_np')->nullable();
            $table->string('description')->nullable();
            $table->string('description_np')->nullable();
            $table->string('icon')->nullable();
            $table->string('color')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Advisors
        Schema::create('advisors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('category_id')->constrained('advisor_categories')->cascadeOnDelete();
            $table->string('name');
            $table->string('name_np')->nullable();
            $table->string('email')->unique()->nullable();
            $table->string('phone')->unique()->nullable();
            $table->text('bio')->nullable();
            $table->text('bio_np')->nullable();
            $table->string('specialization')->nullable();
            $table->string('avatar_url')->nullable();
            $table->decimal('rating', 3, 2)->default(0.00);
            $table->integer('total_reviews')->default(0);
            $table->decimal('consultation_fee', 10, 2)->default(0.00);
            $table->string('currency')->default('NPR');
            $table->json('availability')->nullable(); // available times
            $table->boolean('is_online')->default(false);
            $table->boolean('is_available')->default(true);
            $table->boolean('is_verified')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index(['category_id', 'is_available']);
        });

        // Consultations
        Schema::create('consultations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('advisor_id')->constrained()->cascadeOnDelete();
            $table->string('status')->default('pending'); // pending, confirmed, completed, cancelled
            $table->string('type')->default('chat'); // chat, call, video
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->text('notes')->nullable();
            $table->decimal('amount', 10, 2)->default(0.00);
            $table->string('payment_status')->default('unpaid'); // unpaid, paid, refunded
            $table->string('payment_method')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['advisor_id', 'status']);
        });

        // Advisor reviews
        Schema::create('advisor_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('advisor_id')->constrained()->cascadeOnDelete();
            $table->foreignId('consultation_id')->nullable()->constrained()->nullOnDelete();
            $table->integer('rating'); // 1-5
            $table->text('comment')->nullable();
            $table->text('comment_np')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('advisor_reviews');
        Schema::dropIfExists('consultations');
        Schema::dropIfExists('advisors');
        Schema::dropIfExists('advisor_categories');
    }
};
