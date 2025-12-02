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
        Schema::create('pathway_tariff_summaries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('hospital_id');
            $table->unsignedBigInteger('clinical_pathway_id');
            $table->string('unit_cost_calculation_version', 100); // e.g. 'UC_2025_JAN'
            $table->decimal('estimated_total_cost', 18, 2); // Sum of estimated costs
            $table->decimal('estimated_total_tariff', 18, 2); // Sum of tariffs
            $table->timestamps();

            // Foreign keys
            $table->foreign('hospital_id')->references('id')->on('hospitals')->onDelete('cascade');
            $table->foreign('clinical_pathway_id')->references('id')->on('clinical_pathways')->onDelete('cascade');

            // Indexes
            $table->index('hospital_id');
            $table->index('clinical_pathway_id');
            $table->index('unit_cost_calculation_version');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pathway_tariff_summaries');
    }
};








