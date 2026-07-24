<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('type'); // national_id, passport, driving_license, pan, citizenship, etc.
            $table->string('document_number')->nullable();
            $table->text('encrypted_data')->nullable(); // AES-256 encrypted sensitive fields
            $table->string('file_path')->nullable(); // stored encrypted file
            $table->string('file_name')->nullable();
            $table->string('mime_type')->nullable();
            $table->bigInteger('file_size')->nullable();
            $table->date('issue_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->string('issued_by')->nullable();
            $table->string('status')->default('active'); // active, expired, revoked
            $table->boolean('is_verified')->default(false);
            $table->boolean('reminder_enabled')->default(true);
            $table->integer('reminder_days_before')->default(30);
            $table->json('metadata')->nullable(); // extra fields per document type
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'type']);
            $table->index(['user_id', 'expiry_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
