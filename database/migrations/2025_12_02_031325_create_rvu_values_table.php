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
        Schema::create('rvu_values', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('hospital_id');
            $table->unsignedBigInteger('cost_reference_id');
            $table->smallInteger('period_year'); // e.g. 2025
            $table->tinyInteger('period_month')->nullable(); // 1-12, null if yearly only
            $table->integer('time_factor'); // Waktu dalam menit
            $table->tinyInteger('professionalism_factor'); // 1-5: 1=Perawat, 2=Nurse/Bidan, 3=Dokter Umum, 4=Dokter Spesialis, 5=Dokter Subspesialis
            $table->tinyInteger('difficulty_factor'); // 1-10: 1=Paling Mudah, 10=Paling Sulit
            $table->decimal('normalization_factor', 8, 4)->default(1.0); // Faktor normalisasi, default 1.0
            $table->decimal('rvu_value', 10, 4); // Calculated: (time × professionalism × difficulty) / normalization
            $table->text('notes')->nullable(); // Catatan
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('hospital_id')->references('id')->on('hospitals')->onDelete('cascade');
            $table->foreign('cost_reference_id')->references('id')->on('cost_references')->onDelete('restrict');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');

            // Indexes
            $table->index(['hospital_id', 'cost_reference_id', 'period_year', 'period_month'], 'rvu_hosp_ref_period_idx');
            $table->index('cost_reference_id');
            $table->index(['period_year', 'period_month']);
            $table->index('is_active');

            // Unique constraint: satu RVU per cost reference per periode
            $table->unique(['hospital_id', 'cost_reference_id', 'period_year', 'period_month'], 'rvu_unique_period');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rvu_values');
    }
};
