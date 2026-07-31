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
        Schema::create('quiz_retakes', function (Blueprint $box) {
            $box->id();
            $box->foreignId('quiz_id')->constrained()->onDelete('cascade');
            $box->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $box->foreignId('teacher_id')->constrained('users')->onDelete('cascade');
            $box->timestamp('granted_at');
            $box->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quiz_retakes');
    }
};
