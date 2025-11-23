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
        Schema::create('final_tariffs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('hospital_id');
            $table->unsignedBigInteger('cost_reference_id');
            $table->unsignedBigInteger('tariff_class_id')->nullable();
            $table->unsignedBigInteger('unit_cost_calculation_id');
            $table->string('sk_number', 100); // Official decree number
            $table->decimal('base_unit_cost', 18, 2); // Copied from unit cost
            $table->decimal('margin_percentage', 5, 4); // 0.2 = 20%
            $table->decimal('jasa_sarana', 18, 2); // Facility component
            $table->decimal('jasa_pelayanan', 18, 2); // Professional component
            $table->decimal('final_tariff_price', 18, 2); // Final price to patients/payer
            $table->date('effective_date');
            $table->date('expired_date')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('hospital_id')->references('id')->on('hospitals')->onDelete('cascade');
            $table->foreign('cost_reference_id')->references('id')->on('cost_references')->onDelete('restrict');
            $table->foreign('tariff_class_id')->references('id')->on('tariff_classes')->onDelete('set null');
            $table->foreign('unit_cost_calculation_id')->references('id')->on('unit_cost_calculations')->onDelete('restrict');

            // Indexes
            $table->index('hospital_id');
            $table->index('cost_reference_id');
            $table->index('tariff_class_id');
            $table->index('unit_cost_calculation_id');
            $table->index(['effective_date', 'expired_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('final_tariffs');
    }
};

