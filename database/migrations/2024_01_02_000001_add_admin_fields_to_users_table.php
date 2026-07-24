<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('user')->after('email'); // user, admin, super_admin
            $table->string('avatar')->nullable()->after('role');
            $table->boolean('is_active')->default(true)->after('avatar');
            $table->boolean('cloud_sync_enabled')->default(false)->after('is_active');
            $table->string('preferred_language')->default('en')->after('cloud_sync_enabled');
            $table->string('theme')->default('light')->after('preferred_language');
            $table->json('notification_preferences')->nullable()->after('theme');
            $table->timestamp('email_verified_at')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'role', 'avatar', 'is_active', 'cloud_sync_enabled',
                'preferred_language', 'theme', 'notification_preferences',
            ]);
        });
    }
};
