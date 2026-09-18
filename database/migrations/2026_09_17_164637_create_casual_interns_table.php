<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('casual_interns', function (Blueprint $table) {
            $table->id();
            $table->string('staff_number')->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('national_id')->nullable();
            $table->enum('type', ['casual', 'intern']);
            $table->enum('division', [
                'eye',
                'upper_primary',
                'junior_school',
                'administration',
                'support',
            ]);
            $table->enum('branch', ['juja_road', 'kitisuru', 'south_c']);
            $table->string('job_title')->nullable();
            $table->string('supervisor')->nullable();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->decimal('daily_rate', 10, 2)->nullable();
            $table->string('institution')->nullable();  // for interns
            $table->string('course')->nullable();        // for interns
            $table->enum('status', ['active', 'completed', 'terminated'])->default('active');
            // Promotion
            $table->boolean('promoted')->default(false);
            $table->date('promoted_date')->nullable();
            $table->foreignId('promoted_to_employee_id')
                  ->nullable()
                  ->constrained('employees')
                  ->nullOnDelete();
            $table->text('notes')->nullable();
            $table->foreignId('recorded_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('casual_interns');
    }
};
