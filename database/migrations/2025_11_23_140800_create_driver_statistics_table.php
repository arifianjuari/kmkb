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
        Schema::create('driver_statistics', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('hospital_id');
            $table->tinyInteger('period_month'); // 1-12
            $table->smallInteger('period_year'); // e.g. 2025
            $table->unsignedBigInteger('cost_center_id');
            $table->unsignedBigInteger('allocation_driver_id');
            $table->decimal('value', 18, 4);
            $table->timestamps();

            // Foreign keys
            $table->foreign('hospital_id')->references('id')->on('hospitals')->onDelete('cascade');
            $table->foreign('cost_center_id')->references('id')->on('cost_centers')->onDelete('restrict');
            $table->foreign('allocation_driver_id')->references('id')->on('allocation_drivers')->onDelete('restrict');

            // Indexes
            $table->index(['hospital_id', 'period_year', 'period_month']);
            $table->index('cost_center_id');
            $table->index('allocation_driver_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('driver_statistics');
    }
};






