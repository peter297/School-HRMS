<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('appraisals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->enum('type', ['annual', 'mid_year', 'probation']);
            $table->string('period');
            $table->year('year');
            $table->enum('status', [
                'pending',
                'submitted',
                'approved',
                'rejected',
            ])->default('pending');
            $table->text('line_manager_comments')->nullable();
            $table->text('hr_comments')->nullable();
            $table->text('recommendations')->nullable();
            $table->decimal('overall_score', 4, 2)->nullable();
            $table->enum('overall_grade', [
                'exceeds_expectations',
                'meets_expectations',
                'below_expectations',
            ])->nullable();
            $table->foreignId('submitted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('submitted_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appraisals');
    }
};