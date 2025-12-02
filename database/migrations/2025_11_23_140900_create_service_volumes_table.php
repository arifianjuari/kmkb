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
        Schema::create('service_volumes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('hospital_id');
            $table->tinyInteger('period_month'); // 1-12
            $table->smallInteger('period_year'); // e.g. 2025
            $table->unsignedBigInteger('cost_reference_id');
            $table->unsignedBigInteger('tariff_class_id')->nullable();
            $table->decimal('total_quantity', 18, 2);
            $table->timestamps();

            // Foreign keys
            $table->foreign('hospital_id')->references('id')->on('hospitals')->onDelete('cascade');
            $table->foreign('cost_reference_id')->references('id')->on('cost_references')->onDelete('restrict');
            $table->foreign('tariff_class_id')->references('id')->on('tariff_classes')->onDelete('set null');

            // Indexes
            $table->index(['hospital_id', 'period_year', 'period_month']);
            $table->index('cost_reference_id');
            $table->index('tariff_class_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_volumes');
    }
};








