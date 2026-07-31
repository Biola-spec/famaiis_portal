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
        // Add section_id to school_subjects so subjects can be section-specific
        Schema::table('school_subjects', function (Blueprint $table) {
            $table->unsignedBigInteger('section_id')->nullable()->after('id');
            $table->dropUnique(['name']); // Name uniqueness will be per-section
            $table->unique(['name', 'section_id'], 'subjects_name_section_unique');
        });

        // Add section_id to assign_subjects (subject to class+section assignment)
        if (Schema::hasTable('assign_subjects') && !Schema::hasColumn('assign_subjects', 'section_id')) {
            Schema::table('assign_subjects', function (Blueprint $table) {
                $table->unsignedBigInteger('section_id')->nullable()->after('id');
            });
        }

        // Add section_id to class_marking_settings
        if (!Schema::hasColumn('class_marking_settings', 'section_id')) {
            Schema::table('class_marking_settings', function (Blueprint $table) {
                $table->unsignedBigInteger('section_id')->nullable()->after('class_id');
            });
        }

        // Add section_id to teacher_assignments
        if (!Schema::hasColumn('teacher_assignments', 'section_id')) {
            Schema::table('teacher_assignments', function (Blueprint $table) {
                $table->unsignedBigInteger('section_id')->nullable()->after('teacher_id');
            });
        }

        // Add section_id to student_marks
        if (Schema::hasTable('student_marks') && !Schema::hasColumn('student_marks', 'section_id')) {
            Schema::table('student_marks', function (Blueprint $table) {
                $table->unsignedBigInteger('section_id')->nullable()->after('id');
            });
        }
    }

    public function down(): void
    {
        Schema::table('school_subjects', function (Blueprint $table) {
            $table->dropUnique('subjects_name_section_unique');
            $table->dropColumn('section_id');
            $table->unique('name');
        });

        if (Schema::hasColumn('assign_subjects', 'section_id')) {
            Schema::table('assign_subjects', function (Blueprint $table) {
                $table->dropColumn('section_id');
            });
        }

        if (Schema::hasColumn('class_marking_settings', 'section_id')) {
            Schema::table('class_marking_settings', function (Blueprint $table) {
                $table->dropColumn('section_id');
            });
        }

        if (Schema::hasColumn('teacher_assignments', 'section_id')) {
            Schema::table('teacher_assignments', function (Blueprint $table) {
                $table->dropColumn('section_id');
            });
        }

        if (Schema::hasColumn('student_marks', 'section_id')) {
            Schema::table('student_marks', function (Blueprint $table) {
                $table->dropColumn('section_id');
            });
        }
    }
};
