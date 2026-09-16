<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('terminations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->date('termination_date');
            $table->date('notice_date')->nullable();
            $table->date('last_working_day')->nullable();
            $table->enum('reason', [
                'misconduct',
                'redundancy',
                'contract_end',
                'performance',
                'absenteeism',
                'other',
            ]);
            $table->text('reason_details')->nullable();
            $table->enum('status', [
                'initiated',
                'notice_served',
                'clearance_done',
                'completed',
            ])->default('initiated');
            // Stage tracking
            $table->boolean('notice_served')->default(false);
            $table->date('notice_served_date')->nullable();
            $table->boolean('clearance_done')->default(false);
            $table->date('clearance_done_date')->nullable();
            $table->boolean('final_pay_processed')->default(false);
            $table->date('final_pay_processed_date')->nullable();
            // Supporting docs
            $table->string('supporting_document')->nullable();
            $table->text('hr_notes')->nullable();
            $table->foreignId('recorded_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('terminations');
    }
};