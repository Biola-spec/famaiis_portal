<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('homeworks', function (Blueprint $table) {
            if (!Schema::hasColumn('homeworks', 'status')) {
                $table->string('status', 20)->default('pending')->after('type');
            }
            if (!Schema::hasColumn('homeworks', 'approved_by')) {
                $table->unsignedBigInteger('approved_by')->nullable()->after('status');
            }
            if (!Schema::hasColumn('homeworks', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('approved_by');
            }
            if (!Schema::hasColumn('homeworks', 'recalled_by')) {
                $table->unsignedBigInteger('recalled_by')->nullable()->after('approved_at');
            }
            if (!Schema::hasColumn('homeworks', 'recalled_at')) {
                $table->timestamp('recalled_at')->nullable()->after('recalled_by');
            }
            if (!Schema::hasColumn('homeworks', 'approval_note')) {
                $table->text('approval_note')->nullable()->after('recalled_at');
            }
        });

        DB::table('homeworks')
            ->where('status', 'pending')
            ->whereNull('approved_at')
            ->update([
                'status' => 'approved',
                'approved_at' => now(),
            ]);
    }

    public function down(): void
    {
        Schema::table('homeworks', function (Blueprint $table) {
            $columns = collect(['approval_note', 'recalled_at', 'recalled_by', 'approved_at', 'approved_by', 'status'])
                ->filter(fn ($column) => Schema::hasColumn('homeworks', $column))
                ->all();

            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
