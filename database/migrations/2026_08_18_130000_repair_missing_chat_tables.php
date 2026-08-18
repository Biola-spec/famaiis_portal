<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!$this->tableExists('chat_groups')) {
            Schema::create('chat_groups', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
                $table->timestamps();
            });
        }

        if (!$this->tableExists('group_members')) {
            Schema::create('group_members', function (Blueprint $table) {
                $table->id();
                $table->foreignId('group_id')->constrained('chat_groups')->cascadeOnDelete();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->timestamps();
                $table->unique(['group_id', 'user_id']);
            });
        }

        if (!$this->tableExists('messages')) {
            Schema::create('messages', function (Blueprint $table) {
                $table->id();
                $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('receiver_id')->nullable()->constrained('users')->cascadeOnDelete();
                $table->foreignId('group_id')->nullable()->constrained('chat_groups')->cascadeOnDelete();
                $table->text('message')->nullable();
                $table->string('file_path')->nullable();
                $table->string('file_type')->nullable();
                $table->boolean('is_edited')->default(false);
                $table->timestamp('seen_at')->nullable();
                $table->timestamps();
            });
        } elseif (!Schema::hasColumn('messages', 'is_edited')) {
            Schema::table('messages', function (Blueprint $table) {
                $table->boolean('is_edited')->default(false)->after('message');
            });
        }
    }

    public function down(): void
    {
        // The original chat migration owns these tables.
    }

    private function tableExists(string $table): bool
    {
        return DB::table('information_schema.tables')
            ->where('table_schema', DB::raw('database()'))
            ->where('table_name', $table)
            ->exists();
    }
};
