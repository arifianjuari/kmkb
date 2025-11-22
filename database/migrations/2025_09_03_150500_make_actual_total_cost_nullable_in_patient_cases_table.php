<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('patient_cases', function (Blueprint $table) {
            // Make financial fields nullable to allow empty inputs
            $table->decimal('actual_total_cost', 15, 2)->nullable()->change();
            $table->decimal('cost_variance', 15, 2)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Backfill NULLs to 0.00 to safely revert to NOT NULL
        DB::table('patient_cases')
            ->whereNull('actual_total_cost')
            ->update(['actual_total_cost' => 0.00]);
        DB::table('patient_cases')
            ->whereNull('cost_variance')
            ->update(['cost_variance' => 0.00]);

        Schema::table('patient_cases', function (Blueprint $table) {
            $table->decimal('actual_total_cost', 15, 2)->nullable(false)->change();
            $table->decimal('cost_variance', 15, 2)->nullable(false)->change();
        });
    }
};
