<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('exam_types', function (Blueprint $table) {
            if (!Schema::hasColumn('exam_types', 'description')) {
                $table->string('description')->nullable()->after('name');
            }
            if (!Schema::hasColumn('exam_types', 'active')) {
                $table->boolean('active')->default(true)->after('description');
            }
        });
    }

    public function down(): void
    {
        Schema::table('exam_types', function (Blueprint $table) {
            $dropColumns = [];

            if (Schema::hasColumn('exam_types', 'description')) {
                $dropColumns[] = 'description';
            }
            if (Schema::hasColumn('exam_types', 'active')) {
                $dropColumns[] = 'active';
            }

            if (!empty($dropColumns)) {
                $table->dropColumn($dropColumns);
            }
        });
    }
};
