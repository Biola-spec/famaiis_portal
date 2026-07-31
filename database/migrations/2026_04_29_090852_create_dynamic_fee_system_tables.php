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
        // Fee Types (Tuition, Exam, etc.)
        Schema::create('fee_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('category')->default('mandatory'); // mandatory, optional, one-time, recurring
            $table->timestamps();
        });

        // Fee Structures (defines fees for a section/class)
        Schema::create('fee_structures', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('section_id');
            $table->unsignedBigInteger('class_id')->nullable();
            $table->unsignedBigInteger('term_id')->nullable();
            $table->unsignedBigInteger('year_id'); // Using year_id for session
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->timestamps();

            $table->foreign('section_id')->references('id')->on('school_sections')->onDelete('cascade');
            $table->foreign('class_id')->references('id')->on('student_classes')->onDelete('cascade');
            $table->foreign('term_id')->references('id')->on('terms')->onDelete('cascade');
            $table->foreign('year_id')->references('id')->on('student_years')->onDelete('cascade');
        });

        // Individual Fee Items within a structure
        Schema::create('fee_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('fee_structure_id');
            $table->unsignedBigInteger('fee_type_id');
            $table->decimal('amount', 12, 2);
            $table->timestamps();

            $table->foreign('fee_structure_id')->references('id')->on('fee_structures')->onDelete('cascade');
            $table->foreign('fee_type_id')->references('id')->on('fee_types')->onDelete('cascade');
        });

        // Student's individual fee tracking per section
        Schema::create('student_fees', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('section_id');
            $table->unsignedBigInteger('fee_structure_id');
            $table->decimal('total_due', 12, 2);
            $table->decimal('total_paid', 12, 2)->default(0);
            $table->decimal('balance', 12, 2);
            $table->timestamps();

            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('section_id')->references('id')->on('school_sections')->onDelete('cascade');
            $table->foreign('fee_structure_id')->references('id')->on('fee_structures')->onDelete('cascade');
        });

        // Payment records
        Schema::create('fee_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('section_id');
            $table->unsignedBigInteger('fee_structure_id');
            $table->decimal('amount_paid', 12, 2);
            $table->decimal('balance', 12, 2);
            $table->string('receipt_no')->unique();
            $table->date('payment_date');
            $table->string('payment_method')->nullable();
            $table->timestamps();

            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('section_id')->references('id')->on('school_sections')->onDelete('cascade');
            $table->foreign('fee_structure_id')->references('id')->on('fee_structures')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fee_payments');
        Schema::dropIfExists('student_fees');
        Schema::dropIfExists('fee_items');
        Schema::dropIfExists('fee_structures');
        Schema::dropIfExists('fee_types');
    }
};
