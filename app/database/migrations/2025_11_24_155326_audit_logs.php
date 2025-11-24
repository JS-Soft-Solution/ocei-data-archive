<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users'); // Who performed the action
            $table->string('action'); // create, update, submit, approve, reject
            $table->string('model_type'); // App\Models\ElectricianApplication, etc.
            $table->unsignedBigInteger('model_id'); // ID of the application
            $table->text('changes')->nullable(); // JSON of changes if applicable
            $table->text('comments')->nullable(); // Reason for rejection etc.
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
