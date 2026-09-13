<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            if (!Schema::hasColumn('reports', 'status')) {
                $table->string('status')->default('pending')->after('is_for_all');
            }
            if (!Schema::hasColumn('reports', 'approved_by')) {
                $table->foreignId('approved_by')->nullable()->after('status')->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('reports', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('approved_by');
            }
            if (!Schema::hasColumn('reports', 'recalled_by')) {
                $table->foreignId('recalled_by')->nullable()->after('approved_at')->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('reports', 'recalled_at')) {
                $table->timestamp('recalled_at')->nullable()->after('recalled_by');
            }
            if (!Schema::hasColumn('reports', 'approval_note')) {
                $table->text('approval_note')->nullable()->after('recalled_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            if (Schema::hasColumn('reports', 'recalled_by')) {
                $table->dropForeign(['recalled_by']);
            }
            if (Schema::hasColumn('reports', 'approved_by')) {
                $table->dropForeign(['approved_by']);
            }

            $columns = collect(['approval_note', 'recalled_at', 'recalled_by', 'approved_at', 'approved_by', 'status'])
                ->filter(fn ($column) => Schema::hasColumn('reports', $column))
                ->all();

            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
