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
        // Drop if exists to handle orphaned table from failed migration
        Schema::dropIfExists('service_fee_assignments');
        
        Schema::create('service_fee_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hospital_id')->constrained()->onDelete('cascade');
            $table->foreignId('cost_reference_id')->constrained()->onDelete('cascade'); // Layanan/Tindakan
            $table->foreignId('service_fee_index_id')->constrained('service_fee_indexes')->onDelete('cascade'); // Indeks role
            
            $table->decimal('participation_pct', 5, 2)->default(100.00); // % partisipasi
            $table->integer('headcount')->default(1); // Jumlah tenaga (misal 2 asisten)
            $table->integer('duration_minutes')->nullable(); // Durasi dalam menit
            
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['cost_reference_id', 'service_fee_index_id'], 'service_fee_assignments_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_fee_assignments');
    }
};
