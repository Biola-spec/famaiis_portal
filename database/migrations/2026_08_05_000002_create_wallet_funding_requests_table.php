<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('wallet_funding_requests')) {
            Schema::create('wallet_funding_requests', function (Blueprint $table) {
                $table->id();
                $table->foreignId('wallet_id')->constrained()->cascadeOnDelete();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->decimal('amount', 15, 2);
                $table->string('provider')->default('paystack');
                $table->string('reference')->unique();
                $table->string('transaction_id')->nullable();
                $table->enum('status', ['pending', 'success', 'failed'])->default('pending');
                $table->string('payment_method')->nullable();
                $table->timestamp('paid_at')->nullable();
                $table->json('provider_payload')->nullable();
                $table->timestamps();

                $table->index(['user_id', 'status']);
                $table->index(['wallet_id', 'status']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('wallet_funding_requests');
    }
};
