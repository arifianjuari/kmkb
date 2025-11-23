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
        Schema::table('patient_cases', function (Blueprint $table) {
            // Drop foreign key first if exists to allow column modification
            $table->dropForeign(['clinical_pathway_id']);
        });
        
        Schema::table('patient_cases', function (Blueprint $table) {
            // Make clinical_pathway_id nullable
            $table->unsignedBigInteger('clinical_pathway_id')->nullable()->change();
            
            // Make ina_cbg_code nullable
            $table->string('ina_cbg_code')->nullable()->change();
            
            // Add missing columns
            $table->string('unit_cost_version', 100)->nullable()->after('cost_variance');
            $table->decimal('calculated_total_tariff', 18, 2)->nullable()->after('unit_cost_version');
            $table->string('reimbursement_scheme', 50)->nullable()->after('calculated_total_tariff');
            
            // Recreate foreign key with nullable support
            $table->foreign('clinical_pathway_id')->references('id')->on('clinical_pathways')->onDelete('set null');
            
            // Indexes
            $table->index('unit_cost_version');
            $table->index('reimbursement_scheme');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patient_cases', function (Blueprint $table) {
            // Drop foreign key
            $table->dropForeign(['clinical_pathway_id']);
        });
        
        Schema::table('patient_cases', function (Blueprint $table) {
            // Drop indexes
            $table->dropIndex(['unit_cost_version']);
            $table->dropIndex(['reimbursement_scheme']);
            
            // Drop columns
            $table->dropColumn([
                'unit_cost_version',
                'calculated_total_tariff',
                'reimbursement_scheme'
            ]);
            
            // Revert nullable changes
            $table->unsignedBigInteger('clinical_pathway_id')->nullable(false)->change();
            $table->string('ina_cbg_code')->nullable(false)->change();
            
            // Recreate foreign key
            $table->foreign('clinical_pathway_id')->references('id')->on('clinical_pathways')->onDelete('cascade');
        });
    }
};

