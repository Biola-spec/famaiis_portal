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
        Schema::table('assign_subjects', function (Blueprint $table) {
            $table->integer('teacher_id')->after('subject_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assign_subjects', function (Blueprint $table) {
            $table->dropColumn('teacher_id');
        });
    }
};
