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
        Schema::table('allocation_drivers', function (Blueprint $table) {
            $table->foreignId('unit_of_measurement_id')
                ->nullable()
                ->after('unit_measurement')
                ->constrained('units_of_measurement')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('allocation_drivers', function (Blueprint $table) {
            $table->dropForeign(['unit_of_measurement_id']);
            $table->dropColumn('unit_of_measurement_id');
        });
    }
};
