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
        Schema::create('student_attendances', function (Blueprint $box) {
            $box->id();
            $box->integer('student_id')->comment('student_id=user_id');
            $box->integer('year_id');
            $box->integer('class_id');
            $box->integer('section_id')->nullable();
            $box->date('date');
            $box->string('attend_status');
            $box->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_attendances');
    }
};
