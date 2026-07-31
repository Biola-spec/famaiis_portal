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
        Schema::table('assign_students', function (Blueprint $table) {
            $table->index('student_id');
            $table->index('year_id');
            $table->index('class_id');
            $table->index('group_id');
        });

        Schema::table('discount_students', function (Blueprint $table) {
            $table->index('assign_student_id');
        });

        Schema::table('student_marks', function (Blueprint $table) {
            $table->index('student_id');
            $table->index('class_id');
            $table->index('subject_id');
            $table->index('term_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->index('usertype');
            $table->index('role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assign_students', function (Blueprint $table) {
            $table->dropIndex(['student_id']);
            $table->dropIndex(['year_id']);
            $table->dropIndex(['class_id']);
            $table->dropIndex(['group_id']);
        });

        Schema::table('discount_students', function (Blueprint $table) {
            $table->dropIndex(['assign_student_id']);
        });

        Schema::table('student_marks', function (Blueprint $table) {
            $table->dropIndex(['student_id']);
            $table->dropIndex(['class_id']);
            $table->dropIndex(['subject_id']);
            $table->dropIndex(['term_id']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['usertype']);
            $table->dropIndex(['role']);
        });
    }
};
