<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mock_tests', function (Blueprint $table) {
            $table->foreignId('learning_category_id')->nullable()->after('id')
                  ->constrained()->nullOnDelete();
            $table->string('title_np')->nullable()->after('title');
            $table->text('description')->nullable()->after('title_np');
            $table->text('description_np')->nullable()->after('description');
            $table->boolean('negative_marking')->default(false)->after('pass_percentage');
            $table->decimal('negative_value', 4, 2)->default(0.25)->after('negative_marking');
            $table->boolean('is_featured')->default(false)->after('is_active');
            $table->unsignedBigInteger('created_by')->nullable()->after('is_featured');
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('mock_tests', function (Blueprint $table) {
            $table->dropForeign(['learning_category_id']);
            $table->dropForeign(['created_by']);
            $table->dropColumn([
                'learning_category_id', 'title_np', 'description', 'description_np',
                'negative_marking', 'negative_value', 'is_featured', 'created_by',
            ]);
        });
    }
};
