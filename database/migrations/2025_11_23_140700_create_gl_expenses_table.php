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
        Schema::create('gl_expenses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('hospital_id');
            $table->tinyInteger('period_month'); // 1-12
            $table->smallInteger('period_year'); // e.g. 2025
            $table->unsignedBigInteger('cost_center_id');
            $table->unsignedBigInteger('expense_category_id');
            $table->decimal('amount', 18, 2);
            $table->timestamps();

            // Foreign keys
            $table->foreign('hospital_id')->references('id')->on('hospitals')->onDelete('cascade');
            $table->foreign('cost_center_id')->references('id')->on('cost_centers')->onDelete('restrict');
            $table->foreign('expense_category_id')->references('id')->on('expense_categories')->onDelete('restrict');

            // Indexes
            $table->index(['hospital_id', 'period_year', 'period_month', 'cost_center_id'], 'gl_exp_hosp_period_cc_idx');
            $table->index('expense_category_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gl_expenses');
    }
};

