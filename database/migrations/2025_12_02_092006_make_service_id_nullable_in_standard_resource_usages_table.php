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
            // Jadikan service_id nullable agar bisa menyimpan service manual
            $table->unsignedBigInteger('service_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('standard_resource_usages', function (Blueprint $table) {
            // Kembalikan ke tidak nullable (asumsi awal desain)
            $table->unsignedBigInteger('service_id')->nullable(false)->change();
        });
    }
};
