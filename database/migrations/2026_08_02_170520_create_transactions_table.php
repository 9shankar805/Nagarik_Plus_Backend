<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            
            // Polymorphic relation to allow paying for different things (e.g. FormSubmission, Fine, Tax)
            $table->string('payable_type');
            $table->unsignedBigInteger('payable_id');
            
            $table->decimal('amount', 10, 2);
            $table->string('payment_method')->nullable(); // e.g., 'esewa', 'khalti', 'connectips'
            $table->string('transaction_code')->unique()->nullable(); // Provider's transaction ID
            
            $table->enum('status', ['pending', 'completed', 'failed', 'refunded'])->default('pending');
            $table->json('gateway_response')->nullable(); // Store raw response from gateway
            
            $table->timestamps();
            
            $table->index(['payable_type', 'payable_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('transactions');
    }
};
