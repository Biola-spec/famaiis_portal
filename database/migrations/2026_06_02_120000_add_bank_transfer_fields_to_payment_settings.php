<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payment_settings', function (Blueprint $table) {
            $table->string('bank_name')->nullable()->after('payment_url');
            $table->string('account_name')->nullable()->after('bank_name');
            $table->string('account_number')->nullable()->after('account_name');
            $table->text('transfer_instructions')->nullable()->after('account_number');
            $table->boolean('bank_transfer_enabled')->default(true)->after('transfer_instructions');
        });
    }

    public function down(): void
    {
        Schema::table('payment_settings', function (Blueprint $table) {
            $table->dropColumn([
                'bank_name',
                'account_name',
                'account_number',
                'transfer_instructions',
                'bank_transfer_enabled',
            ]);
        });
    }
};
