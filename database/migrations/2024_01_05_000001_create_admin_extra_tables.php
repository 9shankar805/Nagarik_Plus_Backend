<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('hospitals')) {
            Schema::create('hospitals', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('name_np')->nullable();
                $table->string('address');
                $table->string('address_np')->nullable();
                $table->string('phone');
                $table->string('type')->default('Government');
                $table->double('latitude')->nullable();
                $table->double('longitude')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('banners')) {
            Schema::create('banners', function (Blueprint $table) {
                $table->id();
                $table->string('title_en');
                $table->string('title_np')->nullable();
                $table->string('subtitle_en')->nullable();
                $table->string('subtitle_np')->nullable();
                $table->string('cta1')->nullable();
                $table->string('cta2')->nullable();
                $table->string('bg_gradient')->nullable();
                $table->string('asset_bg')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('social_services')) {
            Schema::create('social_services', function (Blueprint $table) {
                $table->id();
                $table->string('title_en');
                $table->string('title_np')->nullable();
                $table->string('subtitle_en')->nullable();
                $table->string('subtitle_np')->nullable();
                $table->string('icon')->nullable();
                $table->string('color')->nullable();
                $table->string('image_asset')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('vital_events')) {
            Schema::create('vital_events', function (Blueprint $table) {
                $table->id();
                $table->string('title_en');
                $table->string('title_np')->nullable();
                $table->string('bg_color')->nullable();
                $table->string('image_asset')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('vital_events');
        Schema::dropIfExists('social_services');
        Schema::dropIfExists('banners');
        Schema::dropIfExists('hospitals');
    }
};
