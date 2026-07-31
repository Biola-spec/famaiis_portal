<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('terms', function (Blueprint $table) {
            if (!Schema::hasColumn('terms', 'session_id')) {
                $table->unsignedBigInteger('session_id')->nullable()->after('name');
            }
        });

        \DB::table('terms')->whereNull('session_id')->update([
            'session_id' => \DB::raw('student_year_id'),
        ]);
    }

    public function down(): void
    {
        Schema::table('terms', function (Blueprint $table) {
            if (Schema::hasColumn('terms', 'session_id')) {
                $table->dropColumn('session_id');
            }
        });
    }
};
