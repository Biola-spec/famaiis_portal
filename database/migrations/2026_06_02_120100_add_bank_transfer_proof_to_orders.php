<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('transfer_reference')->nullable()->after('paid_at');
            $table->string('transfer_receipt')->nullable()->after('transfer_reference');
            $table->timestamp('receipt_submitted_at')->nullable()->after('transfer_receipt');
            $table->foreignId('verified_by')->nullable()->after('receipt_submitted_at')->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable()->after('verified_by');
            $table->text('verification_note')->nullable()->after('verified_at');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('verified_by');
            $table->dropColumn([
                'transfer_reference',
                'transfer_receipt',
                'receipt_submitted_at',
                'verified_at',
                'verification_note',
            ]);
        });
    }
};
