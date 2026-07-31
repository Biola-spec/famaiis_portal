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
        Schema::create('student_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('class_id')->constrained('student_classes')->onDelete('cascade');
            $table->foreignId('year_id')->nullable()->constrained('student_years')->onDelete('set null');
            $table->foreignId('exam_type_id')->nullable()->constrained('exam_types')->onDelete('set null');
            $table->text('teacher_comment')->nullable();
            $table->json('cognitive_areas')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_assessments');
    }
};
