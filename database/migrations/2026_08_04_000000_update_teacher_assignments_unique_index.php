<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('teacher_assignments', function (Blueprint $table) {
            $table->dropUnique('teacher_assignments_unique');
            $table->unique(['teacher_id', 'class_id', 'subject_id', 'section_id'], 'teacher_assignments_unique');
        });
    }

    public function down(): void
    {
        Schema::table('teacher_assignments', function (Blueprint $table) {
            $table->dropUnique('teacher_assignments_unique');
            $table->unique(['teacher_id', 'class_id', 'subject_id'], 'teacher_assignments_unique');
        });
    }
};
