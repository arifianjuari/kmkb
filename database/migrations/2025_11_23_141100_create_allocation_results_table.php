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
        Schema::create('allocation_results', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('hospital_id');
            $table->tinyInteger('period_month'); // 1-12
            $table->smallInteger('period_year'); // e.g. 2025
            $table->unsignedBigInteger('source_cost_center_id');
            $table->unsignedBigInteger('target_cost_center_id');
            $table->string('allocation_step', 50); // e.g. 'direct', 'step_1', etc.
            $table->decimal('allocated_amount', 18, 2);
            $table->timestamps();

            // Foreign keys
            $table->foreign('hospital_id')->references('id')->on('hospitals')->onDelete('cascade');
            $table->foreign('source_cost_center_id')->references('id')->on('cost_centers')->onDelete('restrict');
            $table->foreign('target_cost_center_id')->references('id')->on('cost_centers')->onDelete('restrict');

            // Indexes
            $table->index(['hospital_id', 'period_year', 'period_month']);
            $table->index('source_cost_center_id');
            $table->index('target_cost_center_id');
            $table->index('allocation_step');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('allocation_results');
    }
};





