<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('teacher_assignments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('teacher_id');
            $table->unsignedBigInteger('class_id');
            $table->unsignedBigInteger('subject_id');
            $table->timestamps();

            $table->unique(['teacher_id', 'class_id', 'subject_id'], 'teacher_assignments_unique');
        });

        $legacyAssignments = \DB::table('assign_subjects')
            ->select('teacher_id', 'class_id', 'subject_id')
            ->whereNotNull('teacher_id')
            ->distinct()
            ->get();

        foreach ($legacyAssignments as $row) {
            \DB::table('teacher_assignments')->insert([
                'teacher_id' => $row->teacher_id,
                'class_id' => $row->class_id,
                'subject_id' => $row->subject_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('teacher_assignments');
    }
};
