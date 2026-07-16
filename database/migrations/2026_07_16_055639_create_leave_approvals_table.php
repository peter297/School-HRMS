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
        Schema::create('leave_approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('leave_id')->constrained()->cascadeOnDelete();
            $table->enum('stage', ['line_manager', 'hr']);
            $table->enum('action', ['approved', 'rejected', 'overridden']);
            $table->foreignId('acted_by')->constrained('users');
            $table->text('notes')->nullable();
            $table->string('task_assigned_to')->nullable();
            $table->text('task_description')->nullable();
            $table->timestamp('acted_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_approvals');
    }
};
