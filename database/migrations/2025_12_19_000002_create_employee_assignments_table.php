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
        Schema::create('employee_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->foreignId('cost_center_id')->constrained()->onDelete('cascade');
            $table->decimal('fte_percentage', 5, 2)->default(1.00)
                  ->comment('FTE percentage 0.00-1.00 (1.00 = full time)');
            $table->date('effective_date')->comment('Tanggal mulai penempatan');
            $table->date('end_date')->nullable()->comment('Tanggal akhir penempatan');
            $table->boolean('is_primary')->default(true)->comment('Penempatan utama');
            $table->text('notes')->nullable();
            $table->timestamps();

            // Index for common queries
            $table->index(['employee_id', 'effective_date']);
            $table->index(['cost_center_id', 'effective_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_assignments');
    }
};
