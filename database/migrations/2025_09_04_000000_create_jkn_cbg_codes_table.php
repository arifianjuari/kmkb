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
        Schema::create('jkn_cbg_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('name', 255);
            $table->text('description')->nullable();

            // Optional classification fields
            $table->enum('service_type', ['Rawat Inap', 'Rawat Jalan'])->nullable();
            $table->tinyInteger('severity_level')->nullable(); // 1/2/3
            $table->string('grouping_version', 50)->nullable();

            // Global tariff for this CBG code
            $table->decimal('tariff', 15, 2);

            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Additional indexes for search/performance
            $table->index('name');
            $table->index(['service_type', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jkn_cbg_codes');
    }
};
