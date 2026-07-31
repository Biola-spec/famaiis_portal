<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('student_marks', function (Blueprint $table) {
            if (!Schema::hasColumn('student_marks', 'subject_id')) {
                $table->unsignedBigInteger('subject_id')->nullable()->after('class_id');
            }
            if (!Schema::hasColumn('student_marks', 'session_id')) {
                $table->unsignedBigInteger('session_id')->nullable()->after('year_id');
            }
            if (!Schema::hasColumn('student_marks', 'ca_score')) {
                $table->decimal('ca_score', 8, 2)->unsigned()->nullable()->after('exam_type_id');
            }
            if (!Schema::hasColumn('student_marks', 'exam_score')) {
                $table->decimal('exam_score', 8, 2)->unsigned()->nullable()->after('ca_score');
            }
            if (!Schema::hasColumn('student_marks', 'project_score')) {
                $table->decimal('project_score', 8, 2)->unsigned()->nullable()->after('exam_score');
            }
            if (!Schema::hasColumn('student_marks', 'ca_breakdown')) {
                $table->json('ca_breakdown')->nullable()->after('project_score');
            }
            if (!Schema::hasColumn('student_marks', 'total_score')) {
                $table->decimal('total_score', 8, 2)->unsigned()->nullable()->after('ca_breakdown');
            }
            if (!Schema::hasColumn('student_marks', 'grade')) {
                $table->string('grade')->nullable()->after('total_score');
            }
        });

        \DB::table('student_marks')->update([
            'session_id' => \DB::raw('year_id'),
        ]);

        Schema::table('student_marks', function (Blueprint $table) {
            try {
                $table->dropUnique('marks_unique');
            } catch (\Throwable $exception) {
                // Existing index may already be absent on some installations.
            }

            $table->unique(
                ['student_id', 'subject_id', 'class_id', 'term_id', 'session_id'],
                'results_student_subject_class_term_session_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('student_marks', function (Blueprint $table) {
            try {
                $table->dropUnique('results_student_subject_class_term_session_unique');
            } catch (\Throwable $exception) {
                // Ignore if missing in rollback path.
            }

            $table->dropColumn([
                'subject_id',
                'session_id',
                'ca_score',
                'exam_score',
                'project_score',
                'ca_breakdown',
                'total_score',
                'grade',
            ]);
        });
    }
};
