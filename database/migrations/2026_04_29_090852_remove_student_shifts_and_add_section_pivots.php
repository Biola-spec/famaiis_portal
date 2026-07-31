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
        // Safely remove shift_id only if it exists
        if (Schema::hasColumn('assign_students', 'shift_id')) {
            Schema::table('assign_students', function (Blueprint $table) {
                $table->dropColumn('shift_id');
            });
        }

        if (Schema::hasColumn('account_student_fees', 'shift_id')) {
            Schema::table('account_student_fees', function (Blueprint $table) {
                $table->dropColumn('shift_id');
            });
        }

        // Drop student_shifts table (no FK dependencies since shift_id removed above)
        Schema::dropIfExists('student_shifts');

        // Create student_section pivot table (multi-section enrollment)
        if (!Schema::hasTable('student_section')) {
            Schema::create('student_section', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('student_id');
                $table->unsignedBigInteger('section_id');
                $table->unsignedBigInteger('class_id');
                $table->unsignedBigInteger('year_id');
                $table->boolean('is_active')->default(true);
                $table->date('enrollment_date')->nullable();
                $table->timestamps();

                $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('section_id')->references('id')->on('school_sections')->onDelete('cascade');
                $table->foreign('class_id')->references('id')->on('student_classes')->onDelete('cascade');
                $table->foreign('year_id')->references('id')->on('student_years')->onDelete('cascade');
            });
        }

        // Create teacher_section pivot table (multi-section teachers)
        if (!Schema::hasTable('teacher_section')) {
            Schema::create('teacher_section', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('teacher_id');
                $table->unsignedBigInteger('section_id');
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->foreign('teacher_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('section_id')->references('id')->on('school_sections')->onDelete('cascade');
            });
        }

        // Keep section_id on users as a "primary/default section" for backward compatibility
        // New section memberships are in student_section / teacher_section pivot tables
    }

    public function down(): void
    {
        Schema::dropIfExists('student_section');
        Schema::dropIfExists('teacher_section');
        
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('section_id')->nullable();
        });

        Schema::create('student_shifts', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        Schema::table('assign_students', function (Blueprint $table) {
            $table->unsignedBigInteger('shift_id')->nullable();
        });

        Schema::table('account_student_fees', function (Blueprint $table) {
            $table->unsignedBigInteger('shift_id')->nullable();
        });
    }
};
