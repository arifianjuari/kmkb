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
        Schema::create('revenue_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hospital_id')->constrained()->onDelete('cascade');
            $table->foreignId('revenue_source_id')->constrained()->onDelete('cascade');
            $table->integer('period_year');
            $table->integer('period_month');
            $table->string('category')->nullable(); // rawat_jalan, rawat_inap, igd, penunjang
            $table->decimal('gross_revenue', 15, 2); // Pendapatan kotor
            $table->decimal('net_revenue', 15, 2)->nullable(); // Pendapatan bersih (setelah potongan)
            $table->integer('claim_count')->nullable(); // Jumlah klaim (untuk BPJS)
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['hospital_id', 'revenue_source_id', 'period_year', 'period_month', 'category'], 'revenue_records_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('revenue_records');
    }
};
