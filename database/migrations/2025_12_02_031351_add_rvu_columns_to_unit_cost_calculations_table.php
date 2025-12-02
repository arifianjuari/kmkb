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
        Schema::table('unit_cost_calculations', function (Blueprint $table) {
            $table->decimal('rvu_value', 10, 4)->nullable()->after('version_label');
            $table->decimal('rvu_weighted_volume', 18, 4)->nullable()->after('rvu_value');
            $table->decimal('unit_cost_with_rvu', 18, 2)->nullable()->after('rvu_weighted_volume');
            $table->unsignedBigInteger('rvu_value_id')->nullable()->after('unit_cost_with_rvu');
            
            // Foreign key to rvu_values
            $table->foreign('rvu_value_id')->references('id')->on('rvu_values')->onDelete('set null');
            $table->index('rvu_value_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('unit_cost_calculations', function (Blueprint $table) {
            $table->dropForeign(['rvu_value_id']);
            $table->dropIndex(['rvu_value_id']);
            $table->dropColumn(['rvu_value', 'rvu_weighted_volume', 'unit_cost_with_rvu', 'rvu_value_id']);
        });
    }
};
