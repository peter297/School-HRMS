<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('disciplinary_cases', function (Blueprint $table) {
            $table->id();
            $table->string('case_number')->unique();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->enum('category', [
                'misconduct',
                'insubordination',
                'absenteeism',
                'performance',
                'harassment',
                'theft',
                'other',
            ]);
            $table->text('description');
            $table->date('incident_date');
            $table->enum('stage', [
                'show_cause_issued',
                'response_received',
                'hearing_held',
                'outcome_recorded',
                'warning_issued',
                'closed',
            ])->default('show_cause_issued');
            $table->enum('outcome', [
                'verbal_warning',
                'written_warning',
                'final_written_warning',
                'dismissal',
                'no_action',
                'pending',
            ])->default('pending');
            $table->text('outcome_notes')->nullable();
            $table->date('outcome_date')->nullable();
            $table->foreignId('recorded_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('disciplinary_cases');
    }
};