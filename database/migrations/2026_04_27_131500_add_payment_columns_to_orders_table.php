<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('payment_reference')->nullable()->unique()->after('status');
            $table->string('payment_transaction_id')->nullable()->after('payment_reference');
            $table->string('payment_method')->nullable()->after('payment_transaction_id');
            $table->string('payment_provider')->default('paystack')->after('payment_method');
            $table->string('payment_status')->default('pending')->after('payment_provider');
            $table->timestamp('paid_at')->nullable()->after('payment_status');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'payment_reference',
                'payment_transaction_id',
                'payment_method',
                'payment_provider',
                'payment_status',
                'paid_at',
            ]);
        });
    }
};
