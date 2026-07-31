<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('learnhub_subjects', function (Blueprint $table) {
            $table->unsignedBigInteger('class_id')->nullable()->after('teacher_id');
            $table->foreign('class_id')->references('id')->on('student_classes')->nullOnDelete();
            $table->index('class_id');
        });
    }

    public function down(): void
    {
        Schema::table('learnhub_subjects', function (Blueprint $table) {
            $table->dropForeign(['class_id']);
            $table->dropIndex(['class_id']);
            $table->dropColumn('class_id');
        });
    }
};
