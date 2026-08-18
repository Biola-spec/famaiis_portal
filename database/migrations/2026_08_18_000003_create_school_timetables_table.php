<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('school_timetables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('year_id')->nullable()->constrained('student_years')->nullOnDelete();
            $table->foreignId('section_id')->nullable()->constrained('school_sections')->nullOnDelete();
            $table->foreignId('class_id')->constrained('student_classes')->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained('school_subjects')->cascadeOnDelete();
            $table->foreignId('teacher_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('day_of_week', 12);
            $table->time('start_time');
            $table->time('end_time');
            $table->string('room', 120)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index(['year_id', 'section_id', 'class_id', 'day_of_week']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('school_timetables');
    }
};
