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
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade');
            $table->integer('class_id');
            $table->integer('subject_id')->nullable();
            $table->string('report_type'); // daily, weekly, monthly, yearly
            $table->string('title');
            $table->longText('description')->nullable();
            $table->string('video_path')->nullable();
            $table->string('video_thumbnail')->nullable();
            $table->boolean('is_for_all')->default(true);
            $table->timestamps();
        });

        Schema::create('report_media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained('reports')->onDelete('cascade');
            $table->string('file_path');
            $table->string('file_type'); // image, document (doc, pdf, excel)
            $table->timestamps();
        });

        Schema::create('report_student', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained('reports')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->timestamp('seen_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_student');
        Schema::dropIfExists('report_media');
        Schema::dropIfExists('reports');
    }
};
