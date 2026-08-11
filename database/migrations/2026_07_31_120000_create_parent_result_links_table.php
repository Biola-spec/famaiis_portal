<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('parent_result_links')) {
            Schema::create('parent_result_links', function (Blueprint $table) {
                $table->id();
                $table->string('token', 12)->unique();
                $table->foreignId('parent_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('student_id')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('year_id')->nullable()->constrained('student_years')->nullOnDelete();
                $table->string('term')->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('expires_at')->nullable();
                $table->boolean('is_active')->default(true);
                $table->unsignedInteger('access_count')->default(0);
                $table->timestamp('last_accessed_at')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('parent_result_links');
    }
};
