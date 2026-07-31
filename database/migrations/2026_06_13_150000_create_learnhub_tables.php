<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('learnhub_subjects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('teacher_id');
            $table->unsignedInteger('total_weeks')->default(12);
            $table->timestamps();

            $table->foreign('teacher_id')->references('id')->on('users')->cascadeOnDelete();
            $table->index('teacher_id');
        });

        Schema::create('learnhub_weeks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('subject_id');
            $table->unsignedInteger('week_number');
            $table->string('title');
            $table->timestamps();

            $table->foreign('subject_id')->references('id')->on('learnhub_subjects')->cascadeOnDelete();
            $table->unique(['subject_id', 'week_number']);
            $table->index('subject_id');
        });

        Schema::create('learnhub_lessons', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('week_id');
            $table->string('title');
            $table->longText('content');
            $table->timestamps();

            $table->foreign('week_id')->references('id')->on('learnhub_weeks')->cascadeOnDelete();
            $table->unique('week_id');
            $table->index('week_id');
        });

        Schema::create('learnhub_cbt_questions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lesson_id');
            $table->unsignedInteger('question_number');
            $table->text('question');
            $table->text('option_a');
            $table->text('option_b');
            $table->text('option_c');
            $table->text('option_d');
            $table->enum('correct_answer', ['A', 'B', 'C', 'D']);
            $table->text('explanation');
            $table->timestamps();

            $table->foreign('lesson_id')->references('id')->on('learnhub_lessons')->cascadeOnDelete();
            $table->index('lesson_id');
        });

        Schema::create('learnhub_student_progress', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('lesson_id');
            $table->timestamp('read_at')->useCurrent();

            $table->foreign('student_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('lesson_id')->references('id')->on('learnhub_lessons')->cascadeOnDelete();
            $table->unique(['student_id', 'lesson_id']);
            $table->index('student_id');
            $table->index('lesson_id');
        });

        Schema::create('learnhub_cbt_attempts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('lesson_id');
            $table->json('answers');
            $table->unsignedInteger('score')->default(0);
            $table->unsignedInteger('total_questions')->default(0);
            $table->boolean('passed')->default(false);
            $table->timestamp('attempted_at')->useCurrent();

            $table->foreign('student_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('lesson_id')->references('id')->on('learnhub_lessons')->cascadeOnDelete();
            $table->index('student_id');
            $table->index('lesson_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('learnhub_cbt_attempts');
        Schema::dropIfExists('learnhub_student_progress');
        Schema::dropIfExists('learnhub_cbt_questions');
        Schema::dropIfExists('learnhub_lessons');
        Schema::dropIfExists('learnhub_weeks');
        Schema::dropIfExists('learnhub_subjects');
    }
};
