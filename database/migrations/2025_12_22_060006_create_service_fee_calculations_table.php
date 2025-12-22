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
        Schema::create('service_fee_calculations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hospital_id')->constrained()->onDelete('cascade');
            $table->foreignId('cost_reference_id')->constrained()->onDelete('cascade');
            $table->foreignId('service_fee_config_id')->constrained()->onDelete('cascade');
            
            $table->integer('period_year');
            $table->integer('period_month');
            
            // Hasil perhitungan
            $table->decimal('total_index_points', 10, 4); // Total poin indeks
            $table->decimal('point_value', 15, 2); // Nilai per poin (Rp)
            $table->decimal('calculated_fee', 15, 2); // Jasa yang dihitung
            
            // Breakdown per role
            $table->json('breakdown')->nullable();
            
            $table->string('calculation_method')->default('index'); // index, percentage, fixed
            $table->foreignId('calculated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['hospital_id', 'cost_reference_id', 'period_year', 'period_month'], 'service_fee_calculations_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_fee_calculations');
    }
};
