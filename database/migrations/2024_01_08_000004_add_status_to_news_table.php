<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('news', function (Blueprint $table) {
            if (!Schema::hasColumn('news', 'status')) {
                $table->enum('status', ['pending', 'approved', 'rejected'])
                    ->default('approved')
                    ->after('is_published');
            }
            if (!Schema::hasColumn('news', 'is_short')) {
                $table->boolean('is_short')->default(false)->after('status');
            }
            if (!Schema::hasColumn('news', 'user_id')) {
                $table->foreignId('user_id')->nullable()
                    ->constrained()->nullOnDelete()
                    ->after('id');
            }
            if (!Schema::hasColumn('news', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable()->after('expires_at');
            }
            if (!Schema::hasColumn('news', 'reviewed_at')) {
                $table->timestamp('reviewed_at')->nullable()->after('rejection_reason');
            }
            if (!Schema::hasColumn('news', 'reviewed_by')) {
                $table->foreignId('reviewed_by')->nullable()
                    ->constrained('users')->nullOnDelete()
                    ->after('reviewed_at');
            }

            if (!collect(Schema::getIndexes('news'))->contains(fn ($i) => $i['name'] === 'news_status_category_index')) {
                $table->index(['status', 'category']);
            }
            if (!collect(Schema::getIndexes('news'))->contains(fn ($i) => $i['name'] === 'news_user_id_status_index')) {
                $table->index(['user_id', 'status']);
            }
        });

        DB::table('news')->whereNull('status')->update(['status' => 'approved']);
    }

    public function down(): void
    {
        Schema::table('news', function (Blueprint $table) {
            $columns = ['status','is_short','user_id','rejection_reason','reviewed_at','reviewed_by'];
            foreach ($columns as $col) {
                if (Schema::hasColumn('news', $col)) {
                    if (in_array($col, ['user_id', 'reviewed_by'], true)) {
                        try {
                            $table->dropForeign(["news_{$col}_foreign"]);
                        } catch (\Throwable) {}
                    }
                    $table->dropColumn($col);
                }
            }
        });
    }
};
