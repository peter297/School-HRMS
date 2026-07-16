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
        Schema::table('leaves', function (Blueprint $table) {
            $table->enum('approval_stage', [
                'pending_line_manager',
                'pending_hr',
                'approved',
                'rejected_line_manager',
                'rejected_hr',
                'cancelled',
            ])->default('pending_line_manager')->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leaves', function (Blueprint $table) {
            $table->dropColumn('approval_stage');
        });
    }
};
