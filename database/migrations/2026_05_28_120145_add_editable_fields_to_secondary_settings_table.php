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
            $cols = [
                'meetings_title' => 'text',
                'meetings_cat1' => 'text',
                'meetings_cat2' => 'text',
                'meetings_cat3' => 'text',
                'meetings_cat4' => 'text',
                'meetings_cat5' => 'text',
                'courses_title' => 'text',
                'facts_title' => 'text',
                'about_item1_title' => 'text',
                'about_item1_text' => 'text',
                'about_item1_btn_text' => 'text',
                'about_item1_btn_link' => 'text',
                'about_item2_title' => 'text',
                'about_item2_text' => 'text',
                'about_item2_btn_text' => 'text',
                'about_item2_btn_link' => 'text',
            ];
            
            foreach ($cols as $col => $type) {
                if (!Schema::hasColumn('secondary_settings', $col)) {
                    $table->text($col)->nullable();
                }
            }

            for ($i = 1; $i <= 4; $i++) {
                foreach (['title', 'price', 'month', 'day', 'desc', 'image'] as $f) {
                    $col = "meeting{$i}_{$f}";
                    if (!Schema::hasColumn('secondary_settings', $col)) {
                        $table->text($col)->nullable();
                    }
                }
            }

            for ($i = 1; $i <= 6; $i++) {
                foreach (['title', 'price', 'image'] as $f) {
                    $col = "course{$i}_{$f}";
                    if (!Schema::hasColumn('secondary_settings', $col)) {
                        $table->text($col)->nullable();
                    }
                }
                $col = "course{$i}_rating";
                if (!Schema::hasColumn('secondary_settings', $col)) {
                    $table->integer($col)->default(5);
                }
            }

            for ($i = 1; $i <= 4; $i++) {
                foreach (['digit', 'title'] as $f) {
                    $col = "fact{$i}_{$f}";
                    if (!Schema::hasColumn('secondary_settings', $col)) {
                        $table->text($col)->nullable();
                    }
                }
            }

            for ($i = 1; $i <= 4; $i++) {
                foreach (['title', 'text'] as $f) {
                    $col = "about_acc{$i}_{$f}";
                    if (!Schema::hasColumn('secondary_settings', $col)) {
                        $table->text($col)->nullable();
                    }
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('secondary_settings', function (Blueprint $table) {
            $table->dropColumn([
                'meetings_title', 'meetings_cat1', 'meetings_cat2', 'meetings_cat3', 'meetings_cat4', 'meetings_cat5',
                'meeting1_title', 'meeting1_price', 'meeting1_month', 'meeting1_day', 'meeting1_desc', 'meeting1_image',
                'meeting2_title', 'meeting2_price', 'meeting2_month', 'meeting2_day', 'meeting2_desc', 'meeting2_image',
                'meeting3_title', 'meeting3_price', 'meeting3_month', 'meeting3_day', 'meeting3_desc', 'meeting3_image',
                'meeting4_title', 'meeting4_price', 'meeting4_month', 'meeting4_day', 'meeting4_desc', 'meeting4_image',
                'courses_title',
                'course1_title', 'course1_price', 'course1_rating', 'course1_image',
                'course2_title', 'course2_price', 'course2_rating', 'course2_image',
                'course3_title', 'course3_price', 'course3_rating', 'course3_image',
                'course4_title', 'course4_price', 'course4_rating', 'course4_image',
                'course5_title', 'course5_price', 'course5_rating', 'course5_image',
                'course6_title', 'course6_price', 'course6_rating', 'course6_image',
                'facts_title',
                'fact1_digit', 'fact1_title',
                'fact2_digit', 'fact2_title',
                'fact3_digit', 'fact3_title',
                'fact4_digit', 'fact4_title',
                'about_item1_title', 'about_item1_text', 'about_item1_btn_text', 'about_item1_btn_link',
                'about_item2_title', 'about_item2_text', 'about_item2_btn_text', 'about_item2_btn_link',
                'about_acc1_title', 'about_acc1_text',
                'about_acc2_title', 'about_acc2_text',
                'about_acc3_title', 'about_acc3_text',
                'about_acc4_title', 'about_acc4_text',
            ]);
        });
    }
};
