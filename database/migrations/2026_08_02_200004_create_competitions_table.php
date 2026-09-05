<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('competitions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('learning_category_id')->constrained()->cascadeOnDelete();
            $table->foreignId('mock_test_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('title_np')->nullable();
            $table->text('description')->nullable();
            $table->text('description_np')->nullable();
            $table->string('banner_url')->nullable();

            // Timing
            $table->timestamp('registration_open_at')->nullable();
            $table->timestamp('registration_close_at')->nullable();
            $table->timestamp('starts_at')->useCurrent();
            $table->timestamp('ends_at')->useCurrent();

            // Rules
            $table->unsignedInteger('max_participants')->nullable(); // null = unlimited
            $table->decimal('entry_fee', 10, 2)->default(0.00);
            $table->boolean('is_free')->default(true);

            // Prize pool
            $table->decimal('prize_pool', 12, 2)->default(0.00);
            $table->json('prize_distribution')->nullable();
            // e.g. [{"rank":1,"label":"1st Prize","amount":5000},{"rank":2,"amount":2000}]

            // Status lifecycle: draft → open → ongoing → completed | cancelled
            $table->string('status')->default('draft');
            $table->timestamp('winners_announced_at')->nullable();

            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();

            $table->timestamps();

            $table->index(['status', 'starts_at']);
            $table->index(['status', 'ends_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('competitions');
    }
};
