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
        Schema::table('cost_references', function (Blueprint $table) {
            // Drop unique constraint on service_code if exists
            $table->dropUnique(['service_code']);
            
            // Add new columns
            $table->unsignedBigInteger('cost_center_id')->nullable()->after('hospital_id');
            $table->unsignedBigInteger('expense_category_id')->nullable()->after('cost_center_id');
            $table->boolean('is_bundle')->default(false)->after('selling_price_total');
            $table->date('active_from')->nullable()->after('is_bundle');
            $table->date('active_to')->nullable()->after('active_from');
            
            // Add foreign keys
            $table->foreign('cost_center_id')->references('id')->on('cost_centers')->onDelete('set null');
            $table->foreign('expense_category_id')->references('id')->on('expense_categories')->onDelete('set null');
            
            // Add composite index for (hospital_id, service_code)
            $table->index(['hospital_id', 'service_code']);
            $table->index('cost_center_id');
            $table->index('expense_category_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cost_references', function (Blueprint $table) {
            // Drop foreign keys
            $table->dropForeign(['cost_center_id']);
            $table->dropForeign(['expense_category_id']);
            
            // Drop indexes
            $table->dropIndex(['hospital_id', 'service_code']);
            $table->dropIndex(['cost_center_id']);
            $table->dropIndex(['expense_category_id']);
            
            // Drop columns
            $table->dropColumn([
                'cost_center_id',
                'expense_category_id',
                'is_bundle',
                'active_from',
                'active_to'
            ]);
            
            // Restore unique constraint on service_code
            $table->unique('service_code');
        });
    }
};

