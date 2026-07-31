<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('fee_id');
            $table->decimal('amount', 12, 2);
            $table->string('reference')->unique();
            $table->string('transaction_id')->nullable();
            $table->enum('status', ['pending', 'success', 'failed'])->default('pending');
            $table->string('payment_method')->nullable();
            $table->string('provider')->default('paystack');
            $table->unsignedBigInteger('paid_by_user_id')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->json('provider_payload')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('fee_id')->references('id')->on('fees')->cascadeOnDelete();
            $table->foreign('paid_by_user_id')->references('id')->on('users')->nullOnDelete();
            $table->index(['fee_id', 'status']);
            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
