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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('staff_number')->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique()->nullable();
            $table->string('phone')->nullable();
            $table->enum('staff_type', ['teacher', 'admin', 'support_staff']);
            $table->enum('division', ['eye', 'upper_primary', 'junior_school', 'administration', 'support_services']);
            $table->string('job_title')->nullable();
            $table->date('date_of_joining');
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->string('national_id')->unique();
            $table->string('kra_pin')->unique()->nullable();
            $table->string('nssf_number')->unique()->nullable();
            $table->string('sha_number')->unique()->nullable();
            $table->string('bank_name')->nullable();
            $table->string('bank_account_number')->nullable();
            $table->enum('employment_status', ['active', 'inactive', 'terminated', 'on_leave'])->default('active');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
