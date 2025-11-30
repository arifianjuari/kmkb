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
        Schema::table('cost_centers', function (Blueprint $table) {
            $table->unsignedBigInteger('tariff_class_id')->nullable()->after('floor');
            $table->foreign('tariff_class_id')->references('id')->on('tariff_classes')->onDelete('set null');
            $table->index('tariff_class_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cost_centers', function (Blueprint $table) {
            $table->dropForeign(['tariff_class_id']);
            $table->dropIndex(['tariff_class_id']);
            $table->dropColumn('tariff_class_id');
        });
    }
};
