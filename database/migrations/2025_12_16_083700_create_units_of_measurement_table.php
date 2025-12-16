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
        Schema::create('units_of_measurement', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hospital_id')->constrained('hospitals')->onDelete('cascade');
            $table->string('code', 30);
            $table->string('name', 100);
            $table->string('symbol', 20)->nullable();
            $table->enum('category', ['area', 'weight', 'count', 'time', 'volume', 'service', 'other'])->default('other');
            $table->enum('context', ['allocation', 'service', 'both'])->default('both');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Unique constraint per hospital
            $table->unique(['hospital_id', 'code']);
            
            // Index for filtering
            $table->index(['hospital_id', 'category']);
            $table->index(['hospital_id', 'context']);
            $table->index(['hospital_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('units_of_measurement');
    }
};
