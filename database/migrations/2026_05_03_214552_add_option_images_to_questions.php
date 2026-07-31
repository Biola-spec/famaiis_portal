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
        Schema::table('questions', function (Blueprint $table) {
            $table->string('image_a')->nullable()->after('option_a');
            $table->string('image_b')->nullable()->after('option_b');
            $table->string('image_c')->nullable()->after('option_c');
            $table->string('image_d')->nullable()->after('option_d');
            $table->string('image_e')->nullable()->after('option_e');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropColumn(['image_a', 'image_b', 'image_c', 'image_d', 'image_e']);
        });
    }
};
