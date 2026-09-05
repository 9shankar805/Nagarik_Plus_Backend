<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('document_templates')) {
            Schema::create('document_templates', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('slug')->unique();
                $table->string('category')->nullable();
                $table->text('description')->nullable();
                $table->string('preview_image')->nullable();
                $table->string('template_file_path')->nullable();
                $table->string('template_file_name')->nullable();
                $table->unsignedBigInteger('template_file_size')->nullable();
                $table->json('placeholder_fields')->nullable();
                $table->unsignedInteger('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->boolean('is_featured')->default(false);
                $table->unsignedInteger('download_count')->default(0);
                $table->text('guidelines')->nullable();
                $table->timestamps();

                $table->index(['category', 'is_active']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('document_templates');
    }
};
