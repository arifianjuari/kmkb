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
        Schema::create('household_expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hospital_id')->constrained()->onDelete('cascade');
            $table->foreignId('cost_center_id')->constrained()->onDelete('cascade');
            $table->foreignId('household_item_id')->constrained()->onDelete('cascade');
            $table->integer('period_month');
            $table->integer('period_year');
            $table->decimal('quantity', 10, 2);
            $table->decimal('unit_price', 15, 2);
            $table->decimal('total_amount', 15, 2)->storedAs('quantity * unit_price');
            $table->timestamps();

            $table->unique(
                ['hospital_id', 'cost_center_id', 'household_item_id', 'period_month', 'period_year'],
                'household_expenses_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('household_expenses');
    }
};
