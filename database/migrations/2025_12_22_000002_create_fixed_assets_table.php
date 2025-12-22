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
        Schema::create('fixed_assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hospital_id')->constrained()->onDelete('cascade');
            $table->foreignId('asset_category_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('cost_center_id')->nullable()->constrained()->nullOnDelete();
            $table->string('asset_code', 50);
            $table->string('name');
            $table->text('description')->nullable();
            
            // Inventory fields for Alkes/Sarpras
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->string('serial_number')->nullable();
            $table->string('manufacturer')->nullable();
            $table->string('location')->nullable();
            $table->enum('condition', ['good', 'fair', 'poor', 'damaged'])->default('good');

            // Financial fields
            $table->date('acquisition_date');
            $table->decimal('acquisition_cost', 18, 2);
            $table->integer('useful_life_years');
            $table->decimal('salvage_value', 18, 2)->default(0);

            // Maintenance & Calibration
            $table->date('warranty_end_date')->nullable();
            $table->date('last_maintenance_date')->nullable();
            $table->date('next_maintenance_date')->nullable();
            $table->date('calibration_due_date')->nullable();

            // Status & Disposal
            $table->enum('status', ['active', 'disposed', 'sold', 'in_repair'])->default('active');
            $table->date('disposal_date')->nullable();
            $table->text('disposal_reason')->nullable();
            $table->decimal('disposal_value', 18, 2)->nullable();

            $table->timestamps();

            $table->unique(['hospital_id', 'asset_code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fixed_assets');
    }
};
