<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('fees', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('class_id');
            $table->string('term');
            $table->string('session');
            $table->string('title');
            $table->decimal('amount', 12, 2);
            $table->timestamps();

            $table->foreign('class_id')->references('id')->on('student_classes')->cascadeOnDelete();
            $table->unique(['class_id', 'term', 'session', 'title'], 'fees_unique_class_term_session_title');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fees');
    }
};
