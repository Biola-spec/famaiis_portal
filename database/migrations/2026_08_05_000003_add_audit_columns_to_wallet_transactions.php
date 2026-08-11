<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('wallet_transactions') && !Schema::hasColumn('wallet_transactions', 'balance_after')) {
            Schema::table('wallet_transactions', function (Blueprint $table) {
                $table->decimal('balance_after', 15, 2)->nullable()->after('amount');
                $table->string('reference_type')->nullable()->after('metadata');
                $table->unsignedBigInteger('reference_id')->nullable()->after('reference_type');
                $table->index(['reference_type', 'reference_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::table('wallet_transactions', function (Blueprint $table) {
            $table->dropIndex(['reference_type', 'reference_id']);
            $table->dropColumn(['balance_after', 'reference_type', 'reference_id']);
        });
    }
};
