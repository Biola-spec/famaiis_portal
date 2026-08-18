<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_assessments', function (Blueprint $table) {
            $table->text('ai_comment_draft')->nullable();
            $table->string('ai_status')->nullable()->index();
            $table->text('ai_flag')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('student_assessments', function (Blueprint $table) {
            $table->dropColumn(['ai_comment_draft', 'ai_status', 'ai_flag']);
        });
    }
};
