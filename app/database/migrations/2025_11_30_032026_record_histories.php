<?php
// database/migrations/2025_11_25_000002_create_record_histories_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('record_histories', function (Blueprint $table) {
            $table->id();
            $table->morphs('historable'); // historable_id (bigint unsigned), historable_type (string)
            $table->foreignId('changed_by')->constrained('users')->onDelete('cascade');
            $table->string('changed_role'); // e.g., 'data_entry_operator'
            $table->string('action'); // e.g., 'created', 'updated', 'status_changed'
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();

//            $table->index(['historable_type', 'historable_id']);
            $table->index('changed_by');
            $table->index('action');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('record_histories');
    }
};
