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
        Schema::create('service_fee_configs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hospital_id')->constrained()->onDelete('cascade');
            $table->string('name'); // "Konfigurasi 2025", "Skenario A"
            $table->integer('period_year');
            
            // Rasio utama (MANUAL, bisa diubah) - Default sesuai Permenkes 85/2015
            $table->decimal('jasa_pelayanan_pct', 5, 2)->default(44.00); // Max 44%
            $table->decimal('jasa_sarana_pct', 5, 2)->default(56.00); // Min 56%
            
            // Distribusi internal Jasa Pelayanan (MANUAL, bisa diubah)
            $table->decimal('pct_medis', 5, 2)->default(60.00); // Jasa Medis
            $table->decimal('pct_keperawatan', 5, 2)->default(25.00); // Jasa Keperawatan
            $table->decimal('pct_penunjang', 5, 2)->default(10.00); // Jasa Penunjang
            $table->decimal('pct_manajemen', 5, 2)->default(5.00); // Jasa Manajemen
            
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['hospital_id', 'name', 'period_year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_fee_configs');
    }
};
