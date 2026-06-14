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
           Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employees_id')->constrained('employees')->onDelete('cascade');
            $table->enum('contract_type', [
                'permanent',
                'fixed-term',
                'probation',
                'part-time',
                'internship',

            ]);
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->integer('renewal_alert_days')->default(30);
            $table->enum('status', ['active', 'expired', 'terminated', 'renewed'])->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};
