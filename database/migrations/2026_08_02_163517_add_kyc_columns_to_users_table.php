<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('kyc_status', ['unverified', 'pending', 'verified', 'rejected'])->default('unverified')->after('email_verified_at');
            $table->string('citizenship_number')->nullable()->after('kyc_status');
            $table->string('citizenship_front_image')->nullable()->after('citizenship_number');
            $table->string('citizenship_back_image')->nullable()->after('citizenship_front_image');
            $table->text('kyc_rejection_reason')->nullable()->after('citizenship_back_image');
            $table->timestamp('kyc_verified_at')->nullable()->after('kyc_rejection_reason');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'kyc_status',
                'citizenship_number',
                'citizenship_front_image',
                'citizenship_back_image',
                'kyc_rejection_reason',
                'kyc_verified_at',
            ]);
        });
    }
};
