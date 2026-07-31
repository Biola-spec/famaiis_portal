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
        Schema::table('student_marks', function (Blueprint $table) {
            $table->integer('term_id')->nullable()->after('exam_type_id');
            // Unique constraint to prevent duplicate results
            $table->unique(['student_id', 'year_id', 'term_id', 'assign_subject_id'], 'marks_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_marks', function (Blueprint $table) {
            $table->dropUnique('marks_unique');
            $table->dropColumn('term_id');
        });
    }
};
