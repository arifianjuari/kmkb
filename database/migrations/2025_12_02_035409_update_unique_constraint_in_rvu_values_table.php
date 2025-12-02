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
        Schema::table('rvu_values', function (Blueprint $table) {
            // Drop old unique constraint
            $table->dropUnique('rvu_unique_period');
            
            // Create new unique constraint that includes cost_center_id
            // This allows the same cost reference to have different RVU values for different cost centers
            $table->unique(
                ['hospital_id', 'cost_reference_id', 'cost_center_id', 'period_year', 'period_month'],
                'rvu_unique_period_cost_center'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rvu_values', function (Blueprint $table) {
            // Drop new unique constraint
            $table->dropUnique('rvu_unique_period_cost_center');
            
            // Restore old unique constraint
            $table->unique(['hospital_id', 'cost_reference_id', 'period_year', 'period_month'], 'rvu_unique_period');
        });
    }
};
