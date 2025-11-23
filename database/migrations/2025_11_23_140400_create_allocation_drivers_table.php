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
        Schema::create('allocation_drivers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('hospital_id');
            $table->string('name', 150);
            $table->string('unit_measurement', 50);
            $table->text('description')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('hospital_id')->references('id')->on('hospitals')->onDelete('cascade');

            // Indexes
            $table->index('hospital_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('allocation_drivers');
    }
};

