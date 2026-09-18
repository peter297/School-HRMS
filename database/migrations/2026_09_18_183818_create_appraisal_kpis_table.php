<?php
// database/migrations/xxxx_create_appraisal_kpis_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('appraisal_kpis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appraisal_id')->constrained()->cascadeOnDelete();
            $table->string('kpi_key');
            $table->string('kpi_label');
            $table->enum('rating', [
                'exceeds_expectations',
                'meets_expectations',
                'below_expectations',
            ])->nullable();
            $table->tinyInteger('score')->nullable();
            $table->text('comments')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appraisal_kpis');
    }
};