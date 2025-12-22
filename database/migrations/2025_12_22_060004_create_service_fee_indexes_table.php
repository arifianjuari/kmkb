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
        Schema::create('service_fee_indexes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hospital_id')->constrained()->onDelete('cascade');
            $table->foreignId('service_fee_config_id')->constrained()->onDelete('cascade');
            
            // Kategori jasa (sesuai distribusi di config)
            $table->string('category'); // medis, keperawatan, penunjang, manajemen
            
            // Kategori profesi (sesuai Employee model)
            $table->string('professional_category'); // doctor_specialist, nurse, etc.
            
            // Role dalam layanan
            $table->string('role'); // dpjp, operator, asisten, perawat_primer, etc.
            
            // Indeks dan faktor
            $table->decimal('base_index', 8, 4); // Indeks dasar
            $table->decimal('education_factor', 5, 2)->default(1.00); // Faktor pendidikan
            $table->decimal('risk_factor', 5, 2)->default(1.00); // Faktor risiko
            $table->decimal('emergency_factor', 5, 2)->default(1.00); // Faktor emergency
            
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['service_fee_config_id', 'professional_category', 'role'], 'service_fee_indexes_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_fee_indexes');
    }
};
