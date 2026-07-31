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
        Schema::table('secondary_settings', function (Blueprint $table) {
            for ($i = 1; $i <= 5; $i++) {
                $table->string("service{$i}_title")->nullable();
                $table->text("service{$i}_desc")->nullable();
                $table->string("service{$i}_icon")->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('secondary_settings', function (Blueprint $table) {
            for ($i = 1; $i <= 5; $i++) {
                $table->dropColumn([
                    "service{$i}_title",
                    "service{$i}_desc",
                    "service{$i}_icon"
                ]);
            }
        });
    }
};
