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
        Schema::table('case_details', function (Blueprint $table) {
            // Add missing columns
            $table->unsignedBigInteger('cost_reference_id')->nullable()->after('pathway_step_id');
            $table->decimal('unit_cost_applied', 18, 2)->nullable()->after('actual_cost');
            $table->decimal('tariff_applied', 18, 2)->nullable()->after('unit_cost_applied');
            
            // Add foreign key
            $table->foreign('cost_reference_id')->references('id')->on('cost_references')->onDelete('set null');
            
            // Indexes
            $table->index('cost_reference_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('case_details', function (Blueprint $table) {
            // Drop foreign key
            $table->dropForeign(['cost_reference_id']);
            
            // Drop indexes
            $table->dropIndex(['cost_reference_id']);
            
            // Drop columns
            $table->dropColumn([
                'cost_reference_id',
                'unit_cost_applied',
                'tariff_applied'
            ]);
        });
    }
};










