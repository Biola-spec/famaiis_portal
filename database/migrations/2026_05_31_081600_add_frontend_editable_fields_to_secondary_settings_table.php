<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('secondary_settings', function (Blueprint $table) {
            $columns = [
                'top_bar_text',
                'hero_button_text',
                'hero_button_link',
                'meetings_categories_title',
                'meetings_button_text',
                'meetings_button_link',
                'about_button_text',
                'about_button_link',
                'facts_video_url',
                'contact_title',
                'contact_button_text',
            ];

            foreach ($columns as $column) {
                if (!Schema::hasColumn('secondary_settings', $column)) {
                    $table->text($column)->nullable();
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('secondary_settings', function (Blueprint $table) {
            $table->dropColumn([
                'top_bar_text',
                'hero_button_text',
                'hero_button_link',
                'meetings_categories_title',
                'meetings_button_text',
                'meetings_button_link',
                'about_button_text',
                'about_button_link',
                'facts_video_url',
                'contact_title',
                'contact_button_text',
            ]);
        });
    }
};
