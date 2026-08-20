<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->index(['usertype', 'role'], 'idx_users_usertype_role');
            $table->index(['surname', 'first_name'], 'idx_users_names');
        });

        Schema::table('assign_students', function (Blueprint $table) {
            $table->index(['year_id', 'class_id', 'student_id'], 'idx_assign_students_search');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('idx_users_usertype_role');
            $table->dropIndex('idx_users_names');
        });

        Schema::table('assign_students', function (Blueprint $table) {
            $table->dropIndex('idx_assign_students_search');
        });
    }
};
