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
        Schema::create('unit_cost_calculations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('hospital_id');
            $table->smallInteger('period_year'); // e.g. 2025
            $table->tinyInteger('period_month')->nullable(); // 1-12, null if yearly only
            $table->unsignedBigInteger('cost_reference_id');
            $table->decimal('direct_cost_material', 18, 2);
            $table->decimal('direct_cost_labor', 18, 2);
            $table->decimal('indirect_cost_overhead', 18, 2);
            $table->decimal('total_unit_cost', 18, 2);
            $table->string('version_label', 100); // e.g. 'UC_2025_JAN'
            $table->timestamps();

            // Foreign keys
            $table->foreign('hospital_id')->references('id')->on('hospitals')->onDelete('cascade');
            $table->foreign('cost_reference_id')->references('id')->on('cost_references')->onDelete('restrict');

            // Indexes
            $table->index(['hospital_id', 'period_year', 'period_month'], 'ucc_hosp_period_idx');
            $table->index('cost_reference_id');
            $table->index('version_label');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unit_cost_calculations');
    }
};

