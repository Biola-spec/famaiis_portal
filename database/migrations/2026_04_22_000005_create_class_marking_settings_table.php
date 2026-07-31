<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('class_marking_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('class_id');
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->unsignedBigInteger('session_id')->nullable();
            $table->unsignedBigInteger('term_id')->nullable();
            $table->unsignedTinyInteger('ca_count')->default(2);
            $table->decimal('ca_weight', 8, 2)->unsigned()->default(40);
            $table->decimal('exam_weight', 8, 2)->unsigned()->default(60);
            $table->boolean('project_enabled')->default(false);
            $table->decimal('total_score', 8, 2)->unsigned()->default(100);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('class_marking_settings');
    }
};
