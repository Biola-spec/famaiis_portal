<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddApprovalFieldsToStudentMarksTable extends Migration
{
    public function up()
    {
        Schema::table('student_marks', function (Blueprint $table) {
            if (!Schema::hasColumn('student_marks', 'status')) {
                $table->string('status', 20)->default('pending')->after('marks');
            }
            if (!Schema::hasColumn('student_marks', 'entered_by')) {
                $table->unsignedBigInteger('entered_by')->nullable()->after('status');
            }
            if (!Schema::hasColumn('student_marks', 'approved_by')) {
                $table->unsignedBigInteger('approved_by')->nullable()->after('entered_by');
            }
            if (!Schema::hasColumn('student_marks', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('approved_by');
            }
            if (!Schema::hasColumn('student_marks', 'recalled_by')) {
                $table->unsignedBigInteger('recalled_by')->nullable()->after('approved_at');
            }
            if (!Schema::hasColumn('student_marks', 'recalled_at')) {
                $table->timestamp('recalled_at')->nullable()->after('recalled_by');
            }
            if (!Schema::hasColumn('student_marks', 'approval_note')) {
                $table->text('approval_note')->nullable()->after('recalled_at');
            }
        });

        DB::table('student_marks')
            ->where('status', 'pending')
            ->whereNull('approved_at')
            ->update([
                'status' => 'approved',
                'approved_at' => now(),
            ]);
    }

    public function down()
    {
        Schema::table('student_marks', function (Blueprint $table) {
            $columns = collect(['approval_note', 'recalled_at', 'recalled_by', 'approved_at', 'approved_by', 'entered_by', 'status'])
                ->filter(fn ($column) => Schema::hasColumn('student_marks', $column))
                ->all();

            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
}
