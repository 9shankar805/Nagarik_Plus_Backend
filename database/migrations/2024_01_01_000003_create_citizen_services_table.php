<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('citizen_services', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title');
            $table->string('title_np')->nullable(); // Nepali
            $table->text('description')->nullable();
            $table->text('description_np')->nullable();
            $table->string('category'); // identity, vehicle, finance, business, legal
            $table->string('icon')->nullable();
            $table->string('color')->nullable();
            $table->text('eligibility')->nullable();
            $table->json('required_documents')->nullable();
            $table->json('application_steps')->nullable();
            $table->string('fee')->nullable();
            $table->date('fee_updated_at')->nullable();
            $table->string('processing_time')->nullable();
            $table->json('faqs')->nullable();
            $table->string('official_url')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('service_offices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained('citizen_services')->cascadeOnDelete();
            $table->string('name');
            $table->string('address');
            $table->string('district');
            $table->string('province');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('office_hours')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_offices');
        Schema::dropIfExists('citizen_services');
    }
};
