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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hospital_id')->constrained()->onDelete('cascade');
            $table->string('employee_number')->comment('NIK/NIP Pegawai');
            $table->string('name');
            $table->date('join_date')->nullable()->comment('Tanggal mulai bekerja');
            $table->date('resign_date')->nullable()->comment('Tanggal berhenti/resign');
            $table->enum('status', ['active', 'inactive', 'resigned'])->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();

            // Unique constraint per hospital
            $table->unique(['hospital_id', 'employee_number']);
            
            // Index for common queries
            $table->index(['hospital_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
