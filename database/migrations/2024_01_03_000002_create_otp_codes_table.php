<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('otp_codes', function (Blueprint $table) {
            $table->id();
            $table->string('identifier'); // phone number or email address
            $table->string('code_hash');  // sha256 hash of the 6-digit OTP
            $table->string('purpose');    // register|reset_pin|verify_email
            $table->timestamp('expires_at');
            $table->timestamp('used_at')->nullable();
            $table->timestamps();

            $table->index('identifier');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('otp_codes');
    }
};
