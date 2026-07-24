<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->unique()->nullable()->after('email');
            $table->string('pin_code')->nullable()->after('password');
            $table->boolean('biometric_enabled')->default(false)->after('pin_code');
            $table->string('device_id')->nullable()->after('biometric_enabled');
            $table->timestamp('last_active_at')->nullable()->after('device_id');
            $table->softDeletes()->after('updated_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone', 'pin_code', 'biometric_enabled',
                'device_id', 'last_active_at', 'deleted_at',
            ]);
        });
    }
};
