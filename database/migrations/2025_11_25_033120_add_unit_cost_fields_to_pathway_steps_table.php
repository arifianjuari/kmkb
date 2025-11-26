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
        Schema::table('pathway_steps', function (Blueprint $table) {
            $table->decimal('unit_cost_applied', 18, 2)->nullable()->after('estimated_cost');
            $table->string('source_unit_cost_version', 100)->nullable()->after('unit_cost_applied');
            $table->decimal('tariff_applied', 18, 2)->nullable()->after('source_unit_cost_version');
            $table->index('source_unit_cost_version');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pathway_steps', function (Blueprint $table) {
            $table->dropIndex(['source_unit_cost_version']);
            $table->dropColumn(['unit_cost_applied', 'source_unit_cost_version', 'tariff_applied']);
        });
    }
};
