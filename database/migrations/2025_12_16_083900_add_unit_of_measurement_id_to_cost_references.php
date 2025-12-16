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
            $table->foreignId('unit_of_measurement_id')
                ->nullable()
                ->after('unit')
                ->constrained('units_of_measurement')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cost_references', function (Blueprint $table) {
            $table->dropForeign(['unit_of_measurement_id']);
            $table->dropColumn('unit_of_measurement_id');
        });
    }
};
