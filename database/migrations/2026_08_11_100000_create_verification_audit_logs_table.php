<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('verification_audit_logs', function (Blueprint $table) {
            $table->id();

            // Who made the request
            $table->unsignedBigInteger('user_id')->nullable()->index();

            // What was verified
            $table->string('document_type', 20);   // nid | licence | pan | citizenship
            $table->string('provider', 100);        // provider adapter name

            // Result (never store the raw document number here)
            $table->string('status', 40);           // VerificationResult::STATUS_* constants
            $table->boolean('verified')->default(false);

            // Encrypted document number hash for deduplication / abuse detection
            // We store only a sha256 hash — never the plaintext number
            $table->string('document_hash', 64)->nullable()->index();

            // Opaque reference token returned by government service (nullable)
            $table->string('reference_token', 255)->nullable();

            // Request metadata (no PII)
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 500)->nullable();

            // Latency for monitoring
            $table->unsignedSmallInteger('duration_ms')->nullable();

            $table->timestamps();

            // Compound indexes for abuse/rate-limit queries
            $table->index(['user_id', 'document_type', 'created_at'], 'val_user_type_date');
            $table->index(['ip_address', 'document_type', 'created_at'], 'val_ip_type_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('verification_audit_logs');
    }
};
