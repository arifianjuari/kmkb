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
        Schema::create('asset_depreciations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fixed_asset_id')->constrained()->onDelete('cascade');
            $table->integer('period_month');
            $table->integer('period_year');
            $table->decimal('depreciation_amount', 18, 2);
            $table->decimal('accumulated_depreciation', 18, 2);
            $table->decimal('book_value', 18, 2);
            $table->boolean('is_synced_to_gl')->default(false);
            $table->foreignId('gl_expense_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();

            $table->unique(['fixed_asset_id', 'period_month', 'period_year'], 'asset_dep_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_depreciations');
    }
};
