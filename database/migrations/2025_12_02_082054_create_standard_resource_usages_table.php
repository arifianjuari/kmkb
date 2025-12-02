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
        Schema::create('standard_resource_usages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('hospital_id');
            $table->unsignedBigInteger('service_id'); // cost_reference untuk tindakan/pemeriksaan
            $table->unsignedBigInteger('bmhp_id'); // cost_reference untuk BMHP
            $table->decimal('quantity', 10, 2); // jumlah BMHP yang diperlukan per 1x tindakan
            $table->string('unit', 50); // satuan (pcs, ml, mg, dll)
            $table->text('notes')->nullable(); // catatan tambahan
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('hospital_id')->references('id')->on('hospitals')->onDelete('cascade');
            $table->foreign('service_id')->references('id')->on('cost_references')->onDelete('restrict');
            $table->foreign('bmhp_id')->references('id')->on('cost_references')->onDelete('restrict');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');

            // Indexes
            $table->index('service_id');
            $table->index('bmhp_id');
            $table->index('is_active');
            $table->index(['hospital_id', 'service_id']);

            // Unique constraint: satu BMHP per service per hospital
            $table->unique(['hospital_id', 'service_id', 'bmhp_id'], 'sru_unique_service_bmhp');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('standard_resource_usages');
    }
};
