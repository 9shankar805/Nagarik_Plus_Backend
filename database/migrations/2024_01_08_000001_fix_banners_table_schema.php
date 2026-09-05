<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('banners')) {
            Schema::table('banners', function (Blueprint $table) {
                if (Schema::hasColumn('banners', 'title_en')) {
                    $table->dropColumn([
                        'title_en',
                        'subtitle_en',
                        'subtitle_np',
                        'cta1',
                        'cta2',
                        'bg_gradient',
                        'asset_bg',
                    ]);
                }

                if (!Schema::hasColumn('banners', 'title')) {
                    $table->string('title')->after('id');
                }
                if (!Schema::hasColumn('banners', 'description')) {
                    $table->text('description')->nullable()->after('title_np');
                }
                if (!Schema::hasColumn('banners', 'image_url')) {
                    $table->string('image_url')->nullable()->after('description');
                }
                if (!Schema::hasColumn('banners', 'link_type')) {
                    $table->string('link_type')->default('none')->after('image_url');
                }
                if (!Schema::hasColumn('banners', 'link_value')) {
                    $table->string('link_value')->nullable()->after('link_type');
                }
                if (!Schema::hasColumn('banners', 'sort_order')) {
                    $table->integer('sort_order')->default(0)->after('link_value');
                }
                if (!Schema::hasColumn('banners', 'starts_at')) {
                    $table->timestamp('starts_at')->nullable()->after('is_active');
                }
                if (!Schema::hasColumn('banners', 'ends_at')) {
                    $table->timestamp('ends_at')->nullable()->after('starts_at');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('banners')) {
            Schema::table('banners', function (Blueprint $table) {
                if (Schema::hasColumn('banners', 'title')) {
                    $table->dropColumn([
                        'title',
                        'description',
                        'image_url',
                        'link_type',
                        'link_value',
                        'sort_order',
                        'starts_at',
                        'ends_at',
                    ]);
                }

                if (!Schema::hasColumn('banners', 'title_en')) {
                    $table->string('title_en')->after('id');
                    $table->string('subtitle_en')->nullable()->after('title_np');
                    $table->string('subtitle_np')->nullable()->after('subtitle_en');
                    $table->string('cta1')->nullable()->after('subtitle_np');
                    $table->string('cta2')->nullable()->after('cta1');
                    $table->string('bg_gradient')->nullable()->after('cta2');
                    $table->string('asset_bg')->nullable()->after('bg_gradient');
                }
            });
        }
    }
};
