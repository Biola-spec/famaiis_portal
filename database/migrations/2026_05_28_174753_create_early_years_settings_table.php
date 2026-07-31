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
        Schema::create('early_years_settings', function (Blueprint $table) {
            $table->id();
            $table->string('hero_video')->nullable();
            $table->string('hero_title')->nullable();
            $table->text('hero_subtitle')->nullable();
            $table->string('about_image')->nullable();
            $table->string('about_title')->nullable();
            $table->text('about_text')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('contact_address')->nullable();

            for ($i = 1; $i <= 5; $i++) {
                $table->string("service{$i}_title")->nullable();
                $table->text("service{$i}_desc")->nullable();
                $table->string("service{$i}_icon")->nullable();
            }

            $table->text('meetings_title')->nullable();
            $table->text('meetings_cat1')->nullable();
            $table->text('meetings_cat2')->nullable();
            $table->text('meetings_cat3')->nullable();
            $table->text('meetings_cat4')->nullable();
            $table->text('meetings_cat5')->nullable();

            for ($i = 1; $i <= 4; $i++) {
                $table->text("meeting{$i}_title")->nullable();
                $table->text("meeting{$i}_price")->nullable();
                $table->text("meeting{$i}_month")->nullable();
                $table->text("meeting{$i}_day")->nullable();
                $table->text("meeting{$i}_desc")->nullable();
                $table->text("meeting{$i}_image")->nullable();
            }

            $table->text('courses_title')->nullable();
            for ($i = 1; $i <= 6; $i++) {
                $table->text("course{$i}_title")->nullable();
                $table->text("course{$i}_price")->nullable();
                $table->integer("course{$i}_rating")->default(5);
                $table->text("course{$i}_image")->nullable();
            }

            $table->text('facts_title')->nullable();
            for ($i = 1; $i <= 4; $i++) {
                $table->text("fact{$i}_digit")->nullable();
                $table->text("fact{$i}_title")->nullable();
            }

            $table->text('about_item1_title')->nullable();
            $table->text('about_item1_text')->nullable();
            $table->text('about_item1_btn_text')->nullable();
            $table->text('about_item1_btn_link')->nullable();
            $table->text('about_item2_title')->nullable();
            $table->text('about_item2_text')->nullable();
            $table->text('about_item2_btn_text')->nullable();
            $table->text('about_item2_btn_link')->nullable();

            for ($i = 1; $i <= 4; $i++) {
                $table->text("about_acc{$i}_title")->nullable();
                $table->text("about_acc{$i}_text")->nullable();
            }

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('early_years_settings');
    }
};
