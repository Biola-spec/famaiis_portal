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
        Schema::table('class_marking_settings', function (Blueprint $table) {
            $table->string('exam_label')->nullable()->default('Exam')->after('exam_weight');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('class_marking_settings', function (Blueprint $table) {
            $table->dropColumn('exam_label');
        });
    }
};
