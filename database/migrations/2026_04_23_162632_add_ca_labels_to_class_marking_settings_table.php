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
            $table->text('ca_labels')->nullable()->after('ca_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('class_marking_settings', function (Blueprint $table) {
            $table->dropColumn('ca_labels');
        });
    }
};
