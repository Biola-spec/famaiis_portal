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
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // 1. Drop Exam Types and Terms tables
        Schema::dropIfExists('exam_types');
        Schema::dropIfExists('terms');

        // 2. Modify student_marks
        if (Schema::hasTable('student_marks')) {
            if (Schema::hasColumn('student_marks', 'exam_type_id')) {
                try { DB::statement('ALTER TABLE student_marks DROP COLUMN exam_type_id'); } catch (\Exception $e) {}
            }
            if (Schema::hasColumn('student_marks', 'term_id')) {
                try { DB::statement('ALTER TABLE student_marks DROP COLUMN term_id'); } catch (\Exception $e) {}
            }
            if (!Schema::hasColumn('student_marks', 'term')) {
                Schema::table('student_marks', function (Blueprint $table) {
                    $table->enum('term', ['1st Term', '2nd Term', '3rd Term'])->nullable();
                });
            }
        }

        // 3. Modify student_assessments
        if (Schema::hasTable('student_assessments')) {
            if (Schema::hasColumn('student_assessments', 'exam_type_id')) {
                try { DB::statement('ALTER TABLE student_assessments DROP COLUMN exam_type_id'); } catch (\Exception $e) {}
            }
            if (!Schema::hasColumn('student_assessments', 'term')) {
                Schema::table('student_assessments', function (Blueprint $table) {
                    $table->enum('term', ['1st Term', '2nd Term', '3rd Term'])->nullable();
                });
            }
        }

        // 4. Modify class_marking_settings
        if (Schema::hasTable('class_marking_settings')) {
            if (Schema::hasColumn('class_marking_settings', 'exam_type_id')) {
                try { DB::statement('ALTER TABLE class_marking_settings DROP COLUMN exam_type_id'); } catch (\Exception $e) {}
            }
            if (Schema::hasColumn('class_marking_settings', 'term_id')) {
                try { DB::statement('ALTER TABLE class_marking_settings DROP COLUMN term_id'); } catch (\Exception $e) {}
            }
            if (!Schema::hasColumn('class_marking_settings', 'term')) {
                Schema::table('class_marking_settings', function (Blueprint $table) {
                    $table->enum('term', ['1st Term', '2nd Term', '3rd Term'])->nullable();
                });
            }
        }

        // 5. Modify quizzes
        if (Schema::hasTable('quizzes')) {
            if (Schema::hasColumn('quizzes', 'exam_type_id')) {
                try { DB::statement('ALTER TABLE quizzes DROP COLUMN exam_type_id'); } catch (\Exception $e) {}
            }
            if (!Schema::hasColumn('quizzes', 'term')) {
                Schema::table('quizzes', function (Blueprint $table) {
                    $table->enum('term', ['1st Term', '2nd Term', '3rd Term'])->nullable();
                });
            }
        }

        // 6. Modify academic_settings
        if (Schema::hasTable('academic_settings')) {
            if (Schema::hasColumn('academic_settings', 'current_term_id')) {
                try { DB::statement('ALTER TABLE academic_settings DROP COLUMN current_term_id'); } catch (\Exception $e) {}
            }
            if (!Schema::hasColumn('academic_settings', 'current_term')) {
                Schema::table('academic_settings', function (Blueprint $table) {
                    $table->enum('current_term', ['1st Term', '2nd Term', '3rd Term'])->nullable();
                });
            }
        }

        // 7. Modify fee_structures (if it exists)
        if (Schema::hasTable('fee_structures')) {
            if (Schema::hasColumn('fee_structures', 'term_id')) {
                try { DB::statement('ALTER TABLE fee_structures DROP COLUMN term_id'); } catch (\Exception $e) {}
            }
            if (!Schema::hasColumn('fee_structures', 'term')) {
                Schema::table('fee_structures', function (Blueprint $table) {
                    $table->enum('term', ['1st Term', '2nd Term', '3rd Term'])->nullable();
                });
            }
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Re-add exam_type_id and term_id if needed, but since we are dropping tables, 
        // a full rollback would require recreating those tables and their data.
        // For simplicity, we'll just allow dropping the new columns.
        
        Schema::table('student_marks', function (Blueprint $table) {
            $table->dropColumn('term');
            $table->integer('exam_type_id')->nullable();
            $table->integer('term_id')->nullable();
        });

        Schema::table('student_assessments', function (Blueprint $table) {
            $table->dropColumn('term');
            $table->integer('exam_type_id')->nullable();
        });

        Schema::table('class_marking_settings', function (Blueprint $table) {
            $table->dropColumn('term');
            $table->integer('exam_type_id')->nullable();
            $table->integer('term_id')->nullable();
        });

        Schema::table('quizzes', function (Blueprint $table) {
            $table->dropColumn('term');
            $table->integer('exam_type_id')->nullable();
        });

        Schema::table('academic_settings', function (Blueprint $table) {
            $table->dropColumn('current_term');
            $table->integer('current_term_id')->nullable();
        });

        Schema::table('fee_structures', function (Blueprint $table) {
            $table->dropColumn('term');
            $table->integer('term_id')->nullable();
        });
    }
};
