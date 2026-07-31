<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('academic_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('current_session_id')->nullable();
            $table->unsignedBigInteger('current_term_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academic_settings');
    }
};
