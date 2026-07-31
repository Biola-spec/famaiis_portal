<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('learnhub_live_sessions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('subject_id');
            $table->unsignedBigInteger('lesson_id')->nullable();
            $table->unsignedBigInteger('teacher_id');
            $table->string('title');
            $table->string('room_name')->unique();
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->enum('status', ['scheduled', 'live', 'ended'])->default('scheduled');
            $table->timestamps();

            $table->foreign('subject_id')->references('id')->on('learnhub_subjects')->cascadeOnDelete();
            $table->foreign('lesson_id')->references('id')->on('learnhub_lessons')->nullOnDelete();
            $table->foreign('teacher_id')->references('id')->on('users')->cascadeOnDelete();
            $table->index(['subject_id', 'status']);
        });

        Schema::table('learnhub_cbt_attempts', function (Blueprint $table) {
            $table->unsignedInteger('game_points')->default(0)->after('passed');
            $table->unsignedInteger('max_streak')->default(0)->after('game_points');
            $table->unsignedInteger('time_seconds')->nullable()->after('max_streak');
        });
    }

    public function down(): void
    {
        Schema::table('learnhub_cbt_attempts', function (Blueprint $table) {
            $table->dropColumn(['game_points', 'max_streak', 'time_seconds']);
        });

        Schema::dropIfExists('learnhub_live_sessions');
    }
};
