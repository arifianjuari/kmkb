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
        Schema::table('standard_resource_usages', function (Blueprint $table) {
            $table->unsignedBigInteger('unit_of_measurement_id')->nullable()->after('unit');
            $table->foreign('unit_of_measurement_id')->references('id')->on('units_of_measurement')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('standard_resource_usages', function (Blueprint $table) {
            $table->dropForeign(['unit_of_measurement_id']);
            $table->dropColumn('unit_of_measurement_id');
        });
    }
};
