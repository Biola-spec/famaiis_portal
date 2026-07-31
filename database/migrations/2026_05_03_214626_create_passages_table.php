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
        Schema::create('passages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained()->onDelete('cascade');
            $table->longText('content');
            $table->string('image')->nullable();
            $table->integer('start_number');
            $table->integer('end_number');
            $table->timestamps();
        });

        // Remove old passage columns from questions
        Schema::table('questions', function (Blueprint $table) {
            $table->dropColumn(['passage', 'passage_start', 'passage_end']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('passages');
        
        Schema::table('questions', function (Blueprint $table) {
            $table->longText('passage')->nullable()->after('quiz_id');
            $table->integer('passage_start')->nullable()->after('passage');
            $table->integer('passage_end')->nullable()->after('passage_start');
        });
    }
};
