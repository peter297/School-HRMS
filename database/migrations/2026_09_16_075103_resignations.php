<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('resignations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->date('resignation_date');
            $table->date('notice_end_date');
            $table->date('last_working_day')->nullable();
            $table->enum('notice_period', ['1_week', '2_weeks', '1_month', '2_months', '3_months'])->default('1_month');
            $table->enum('reason', [
                'personal',
                'better_opportunity',
                'relocation',
                'health',
                'further_studies',
                'retirement',
                'other',
            ]);
            $table->text('reason_details')->nullable();
            $table->enum('status', [
                'notice_served',
                'last_working_day',
                'clearance_done',
                'final_pay_processed',
                'completed',
            ])->default('notice_served');
            $table->boolean('notice_served')->default(false);
            $table->date('notice_served_date')->nullable();
            $table->boolean('last_working_day_confirmed')->default(false);
            $table->date('last_working_day_confirmed_date')->nullable();
            $table->boolean('clearance_done')->default(false);
            $table->date('clearance_done_date')->nullable();
            $table->boolean('final_pay_processed')->default(false);
            $table->date('final_pay_processed_date')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('recorded_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resignations');
    }
};