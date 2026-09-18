<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('disciplinary_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('disciplinary_case_id')
                  ->constrained('disciplinary_cases')
                  ->cascadeOnDelete();
            $table->enum('type', [
                'show_cause_letter',
                'response_to_show_cause',
                'hearing_record',
                'warning_letter',
            ]);
            $table->enum('warning_level', [
                'verbal',
                'written',
                'final_written',
                'dismissal',
                'none',
            ])->default('none');
            $table->date('document_date');
            $table->text('content');
            $table->string('response_text')->nullable();
            $table->date('response_date')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('disciplinary_documents');
    }
};