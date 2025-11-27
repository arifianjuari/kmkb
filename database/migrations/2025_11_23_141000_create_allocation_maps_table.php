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
        Schema::create('allocation_maps', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('hospital_id');
            $table->unsignedBigInteger('source_cost_center_id');
            $table->unsignedBigInteger('allocation_driver_id');
            $table->integer('step_sequence');
            $table->timestamps();

            // Foreign keys
            $table->foreign('hospital_id')->references('id')->on('hospitals')->onDelete('cascade');
            $table->foreign('source_cost_center_id')->references('id')->on('cost_centers')->onDelete('restrict');
            $table->foreign('allocation_driver_id')->references('id')->on('allocation_drivers')->onDelete('restrict');

            // Indexes
            $table->index('hospital_id');
            $table->index('source_cost_center_id');
            $table->index('allocation_driver_id');
            $table->index('step_sequence');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('allocation_maps');
    }
};



